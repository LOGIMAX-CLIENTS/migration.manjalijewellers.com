<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MY_Controller — Base Controller for Three-Layer Override System
 *
 * Provides get_flag() shortcut and client metadata to all controllers.
 * All application controllers that need override awareness should extend
 * this class (or it can be transparent via CI's MY_ autoload).
 */
class MY_Controller extends CI_Controller
{
    /**
     * Current client ID
     * @var string
     */
    public $client_id;

    /**
     * Full client config array (from JSON)
     * @var array
     */
    public $client_config;

    public function __construct()
    {
        parent::__construct();

        $this->client_id     = get_client_id();
        $this->client_config = get_client_config();

        log_message('debug', 'Override: MY_Controller loaded for client "' . $this->client_id . '"');
    }

    /**
     * Shortcut to read a flag from the client config
     *
     * @param  string $key      Flag key name
     * @param  mixed  $default  Default value if flag not set
     * @return mixed
     */
    public function get_flag($key, $default = null)
    {
        return get_flag($key, $default);
    }

    /**
     * Check if a feature pack is enabled
     *
     * @param  string $name  Feature name
     * @return bool
     */
    public function has_feature($name)
    {
        return has_feature($name);
    }
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
