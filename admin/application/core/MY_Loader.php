<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MY_Loader — Override-Aware Loader for Three-Layer Override System
 *
 * Intercepts view(), model(), and helper() calls to check
 * client and feature pack directories before falling back to core.
 *
 * Resolution order:
 *   1. clients/{CLIENT_ID}/{type}/...  (client-specific)
 *   2. features/{feature}/{type}/...   (feature packs)
 *   3. admin/application/{type}/...    (core — default)
 */
class MY_Loader extends CI_Loader
{
    /**
     * Repo root path (parent of admin/)
     * @var string
     */
    private $_repo_root;

    public function __construct()
    {
        parent::__construct();
        $this->_repo_root = realpath(FCPATH . '..') . DIRECTORY_SEPARATOR;
    }

    // -----------------------------------------------------------------------
    // VIEW Override
    // -----------------------------------------------------------------------

    /**
     * Load a view with override resolution
     *
     * @param  string $view    View name (e.g. 'welcome_message' or 'billing/print/bill_format_2')
     * @param  array  $vars    Variables to pass to view
     * @param  bool   $return  Whether to return the view as string
     * @return mixed
     */
    public function view($view, $vars = array(), $return = FALSE)
    {
        // Kill switch check
        if ( ! defined('ENABLE_OVERRIDES') || ! ENABLE_OVERRIDES) {
            return parent::view($view, $vars, $return);
        }

        $client_id = defined('CLIENT_ID') ? CLIENT_ID : 'default';

        // Layer 1: Client-specific view
        $client_view_path = $this->_repo_root . 'clients/' . $client_id . '/views/';
        $client_view_file = $client_view_path . $view . '.php';

        if ($client_id !== 'default' && file_exists($client_view_file)) {
            log_message('debug', 'Override resolved: view "' . $view . '" → clients/' . $client_id . '/views/' . $view . '.php');
            // Add the client view path temporarily
            $this->_ci_view_paths = array($client_view_path => TRUE) + $this->_ci_view_paths;
            $result = parent::view($view, $vars, $return);
            // Remove temporary path to avoid polluting subsequent calls
            array_shift($this->_ci_view_paths);
            return $result;
        }

        // Layer 2: Feature pack views
        $config = function_exists('get_client_config') ? get_client_config() : [];
        if ( ! empty($config['features'])) {
            foreach ($config['features'] as $feature) {
                $feature_view_path = $this->_repo_root . 'features/' . $feature . '/views/';
                $feature_view_file = $feature_view_path . $view . '.php';

                if (file_exists($feature_view_file)) {
                    log_message('debug', 'Override resolved: view "' . $view . '" → features/' . $feature . '/views/' . $view . '.php');
                    $this->_ci_view_paths = array($feature_view_path => TRUE) + $this->_ci_view_paths;
                    $result = parent::view($view, $vars, $return);
                    array_shift($this->_ci_view_paths);
                    return $result;
                }
            }
        }

        // Layer 3: Core (default behavior)
        log_message('debug', 'Override: no override found for view "' . $view . '", using core');
        return parent::view($view, $vars, $return);
    }

    // -----------------------------------------------------------------------
    // MODEL Override
    // -----------------------------------------------------------------------

    /**
     * Load a model with override resolution
     *
     * @param  string $model     Model name
     * @param  string $name      Alias
     * @param  mixed  $db_group  Database group
     * @return object
     */
    public function model($model, $name = '', $db_group = FALSE)
    {
        // CI3's parent model() accepts arrays — delegate directly
        if (is_array($model)) {
            return parent::model($model, $name, $db_group);
        }

        // Kill switch check
        if ( ! defined('ENABLE_OVERRIDES') || ! ENABLE_OVERRIDES) {
            return parent::model($model, $name, $db_group);
        }

        $client_id = defined('CLIENT_ID') ? CLIENT_ID : 'default';

        // Normalize model path (handle subdirectories like 'billing/billing_model')
        $model_path = str_replace('\\', '/', $model);

        // Layer 1: Client-specific model
        $client_model_path = $this->_repo_root . 'clients/' . $client_id . '/models/';
        $client_model_file = $client_model_path . $model_path . '.php';

        if ($client_id !== 'default' && file_exists($client_model_file)) {
            log_message('debug', 'Override resolved: model "' . $model . '" → clients/' . $client_id . '/models/' . $model_path . '.php');
            $this->_ci_model_paths = array($client_model_path => TRUE) + $this->_ci_model_paths;
            $result = parent::model($model, $name, $db_group);
            array_shift($this->_ci_model_paths);
            return $result;
        }

        // Layer 2: Feature pack models
        $config = function_exists('get_client_config') ? get_client_config() : [];
        if ( ! empty($config['features'])) {
            foreach ($config['features'] as $feature) {
                $feature_model_path = $this->_repo_root . 'features/' . $feature . '/models/';
                $feature_model_file = $feature_model_path . $model_path . '.php';

                if (file_exists($feature_model_file)) {
                    log_message('debug', 'Override resolved: model "' . $model . '" → features/' . $feature . '/models/' . $model_path . '.php');
                    $this->_ci_model_paths = array($feature_model_path => TRUE) + $this->_ci_model_paths;
                    $result = parent::model($model, $name, $db_group);
                    array_shift($this->_ci_model_paths);
                    return $result;
                }
            }
        }

        // Layer 3: Core (default behavior)
        log_message('debug', 'Override: no override found for model "' . $model . '", using core');
        return parent::model($model, $name, $db_group);
    }

    // -----------------------------------------------------------------------
    // HELPER Override
    // -----------------------------------------------------------------------

    /**
     * Load helpers with override resolution
     *
     * @param  mixed $helpers  Helper name or array of names
     * @return void
     */
    public function helper($helpers = array())
    {
        // Kill switch check
        if ( ! defined('ENABLE_OVERRIDES') || ! ENABLE_OVERRIDES) {
            return parent::helper($helpers);
        }

        // Normalize to array
        if ( ! is_array($helpers)) {
            $helpers = array($helpers);
        }

        $client_id = defined('CLIENT_ID') ? CLIENT_ID : 'default';

        foreach ($helpers as $helper) {
            // Remove _helper suffix if present for path resolution
            $helper_name = str_replace('_helper', '', $helper);
            $helper_filename = $helper_name . '_helper.php';

            // Layer 1: Client-specific helper
            $client_helper_path = $this->_repo_root . 'clients/' . $client_id . '/helpers/';
            $client_helper_file = $client_helper_path . $helper_filename;

            if ($client_id !== 'default' && file_exists($client_helper_file)) {
                log_message('debug', 'Override resolved: helper "' . $helper . '" → clients/' . $client_id . '/helpers/' . $helper_filename);
                // Include the override helper manually, then let parent load core
                // Override helpers extend/override core functions
                include_once($client_helper_file);
            }

            // Layer 2: Feature pack helpers
            $config = function_exists('get_client_config') ? get_client_config() : [];
            if ( ! empty($config['features'])) {
                foreach ($config['features'] as $feature) {
                    $feature_helper_path = $this->_repo_root . 'features/' . $feature . '/helpers/';
                    $feature_helper_file = $feature_helper_path . $helper_filename;

                    if (file_exists($feature_helper_file)) {
                        log_message('debug', 'Override resolved: helper "' . $helper . '" → features/' . $feature . '/helpers/' . $helper_filename);
                        include_once($feature_helper_file);
                    }
                }
            }
        }

        // Layer 3: Always load core helpers via parent
        return parent::helper($helpers);
    }
}

/* End of file MY_Loader.php */
/* Location: ./application/core/MY_Loader.php */
