<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MY_Router — Override-Aware Router for Three-Layer Override System
 *
 * Records controller overrides during routing, but does NOT load them.
 * CodeIgniter.php handles the actual controller instantiation — we hook
 * in at that point by using APPPATH-relative includes.
 *
 * Strategy: Instead of requiring override files during routing (when
 * CI_Controller doesn't exist yet), we store the override path and
 * register an spl_autoload function that loads the override when
 * PHP tries to instantiate the override class.
 */
class MY_Router extends CI_Router
{
    /**
     * Override file path (if found)
     * @var string|null
     */
    public $override_controller_path = null;

    /**
     * Override class name (e.g. 'Welcome_override')
     * @var string|null
     */
    public $override_class_name = null;

    /**
     * Original class name before override
     * @var string|null
     */
    public $original_class = null;

    /**
     * Validate the request and check for controller overrides
     */
    public function _validate_request($segments)
    {
        // Let parent do the normal validation first
        $segments = parent::_validate_request($segments);

        // Kill switch check
        if ( ! defined('ENABLE_OVERRIDES') || ! ENABLE_OVERRIDES) {
            return $segments;
        }

        $client_id = defined('CLIENT_ID') ? CLIENT_ID : 'default';

        // No overrides for default client
        if ($client_id === 'default') {
            return $segments;
        }

        // Get the controller class name from segments
        if ( ! empty($segments)) {
            $class = $segments[0];
        } else {
            $class = $this->default_controller;
        }

        // Check for client-specific controller override
        $repo_root = realpath(FCPATH . '..') . DIRECTORY_SEPARATOR;
        $override_path = $repo_root . 'clients' . DIRECTORY_SEPARATOR . $client_id . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $class . '.php';

        if (file_exists($override_path)) {
            $override_class = ucfirst($class) . '_override';

            // Store for later — DO NOT require_once here (CI_Controller not yet loaded)
            $this->override_controller_path = $override_path;
            $this->override_class_name = $override_class;
            $this->original_class = $class;

            // Register autoloader that will fire when CodeIgniter tries to instantiate
            $core_path = APPPATH . 'controllers' . DIRECTORY_SEPARATOR;
            $stored_override_path = $override_path;
            $stored_override_class = $override_class;
            $stored_class = ucfirst($class);
            $stored_core_path = $core_path . $class . '.php';

            spl_autoload_register(function($requested_class) use ($stored_override_class, $stored_override_path, $stored_class, $stored_core_path) {
                if ($requested_class === $stored_override_class) {
                    // Make sure the core class is loaded first
                    if ( ! class_exists($stored_class, false) && file_exists($stored_core_path)) {
                        require_once($stored_core_path);
                    }
                    require_once($stored_override_path);
                }
            });

            // Tell CI to use the override class
            $this->set_class($override_class);

            log_message('debug', 'Override: controller override registered for "' . $class . '" → clients/' . $client_id . '/controllers/' . $class . '.php (class: ' . $override_class . ')');
        } else {
            log_message('debug', 'Override: no controller override for "' . $class . '", using core');
        }

        return $segments;
    }
}

/* End of file MY_Router.php */
/* Location: ./application/core/MY_Router.php */
