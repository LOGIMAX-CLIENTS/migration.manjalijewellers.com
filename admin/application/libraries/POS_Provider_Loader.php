<?php
/**
 * POS Provider Loader
 * 
 * Dynamically discovers and loads POS provider plugin classes.
 * Add a new provider by:
 *   1. Creating POS_Providername.php in this directory
 *   2. Implementing POS_Provider_Interface
 *   3. Adding a record in ret_pos_providers DB table
 */
class POS_Provider_Loader {

    private $providers = array();
    private $providerDir;

    public function __construct()
    {
        $this->providerDir = dirname(__FILE__) . '/pos_providers/';
        $this->_discoverProviders();
    }

    /**
     * Get a provider instance by its code (e.g. 'pinelabs', 'phonepe_dqr')
     * @param string $providerCode
     * @return POS_Provider_Interface|null
     */
    public function getProvider($providerCode)
    {
        $code = strtolower($providerCode);
        if(isset($this->providers[$code])){
            return $this->providers[$code];
        }
        return null;
    }

    /**
     * Get all registered provider codes
     * @return array
     */
    public function getRegisteredProviders()
    {
        return array_keys($this->providers);
    }

    /**
     * Auto-discover provider classes in pos_providers/ directory
     */
    private function _discoverProviders()
    {
        // Load the interface first
        $interfaceFile = $this->providerDir . 'POS_Provider_Interface.php';
        if(file_exists($interfaceFile)){
            require_once($interfaceFile);
        }

        // Scan directory for provider files (POS_*.php)
        $files = glob($this->providerDir . 'POS_*.php');
        foreach($files as $file){
            $basename = basename($file, '.php');
            
            // Skip the interface itself
            if($basename === 'POS_Provider_Interface') continue;

            require_once($file);

            // Check if class exists and implements our interface
            if(class_exists($basename)){
                $instance = new $basename();
                if($instance instanceof POS_Provider_Interface){
                    $code = strtolower($instance->getProviderCode());
                    $this->providers[$code] = $instance;
                }
            }
        }
    }
}
