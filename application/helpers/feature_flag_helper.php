<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('feature_flag')) {
    /**
     * Check if a feature flag is enabled.
     *
     * Reads the `feature_flags` JSON column from `chit_settings` once per
     * request (static cache), then returns the status of the requested flag code.
     *
     * Usage in any controller or view:
     *   feature_flag('USE_EXTERNAL_CUSTOMER_DB')   // true or false
     *   feature_flag('SHOW_REGION', true)           // returns true if flag not defined
     *
     * @param  string $code     The flag code, e.g. 'SHOW_REGION'
     * @param  bool   $default  Value to return when the flag is not defined in DB
     *                          (default: false — fail-closed)
     * @return bool
     */
    function feature_flag($code, $default = false)
    {
        static $_ff_cache = null;   // parsed once per request

        if ($_ff_cache === null) {
            $CI  = &get_instance();
            $res = $CI->db->query(
                "SELECT IFNULL(feature_flags,'[]') AS feature_flags FROM chit_settings LIMIT 1"
            );
            $raw = ($res && $res->num_rows() > 0) ? $res->row('feature_flags') : '[]';

            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decoded = json_decode(stripslashes($raw), true);
            }
            $_ff_cache = is_array($decoded) ? $decoded : [];
        }

        foreach ($_ff_cache as $flag) {
            if (isset($flag['code']) && $flag['code'] === $code) {
                return (int)$flag['status'] === 1;
            }
        }

        return $default;
    }
}
