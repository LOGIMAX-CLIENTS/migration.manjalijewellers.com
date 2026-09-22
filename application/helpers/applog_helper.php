<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * applog — one log root, one entry format, one trigger label.
 *
 * Why this exists
 * ---------------
 * Log paths used to be written as the relative string 'log/'.date("Y-m-d"), which
 * resolves against the *front controller* directory. The same helper code reached
 * through /index.php landed in <webroot>/log/<date>/... while the same code reached
 * through /admin/index.php landed in <webroot>/admin/log/<date>/..., so one logical
 * channel (directAPI, existing, ...) was split across two trees. applog_base_dir()
 * resolves an absolute root instead, so every entry for a channel is in one place.
 *
 * Entries also used to say only "Model : registration_model", which does not tell you
 * whether the row came from the mobile app, the collection app or a CRON service.
 * applog_source() derives that label from the live route, so every line names its
 * trigger without each call site having to pass it.
 *
 * NOTE: this file is duplicated into admin/application/helpers/ as a thin require
 * shim (CodeIgniter 2 only loads helpers from its own APPPATH). Edit THIS file.
 */

if (!function_exists('applog_base_dir')) {
    /**
     * Absolute, canonical log root: <webroot>/admin/log
     *
     * FCPATH is the directory of the executing front controller, i.e. <webroot>/ for
     * the customer app and <webroot>/admin/ for the admin app. Both are normalised to
     * the same root here so a channel never splits across two trees again.
     */
    function applog_base_dir()
    {
        static $base = null;
        if ($base !== null) {
            return $base;
        }

        $fcpath  = rtrim(str_replace('\\', '/', FCPATH), '/');
        $webroot = $fcpath;

        // Reached through a sub-application (admin/, api/, ...): step up to the webroot.
        if (in_array(strtolower(basename($fcpath)), array('admin', 'api'), true)) {
            $webroot = rtrim(str_replace('\\', '/', dirname($fcpath)), '/');
        }

        // Standard mono-deploy: admin/ exists and owns the log tree. A standalone
        // deployment without admin/ falls back to its own webroot.
        $base = is_dir($webroot . '/admin') ? $webroot . '/admin/log' : $webroot . '/log';

        return $base;
    }
}

if (!function_exists('applog_channels')) {
    /**
     * channel directory => human label (used by the log viewer dropdown).
     */
    function applog_channels()
    {
        return array(
            'customer_reg_update' => 'Customer_reg update',
            'customer_reg_entry'  => 'Customer_reg entry',
            'transaction_entry'   => 'Transaction entry',
            'directAPI'           => 'Direct API',
            'existing'            => 'Existing Logs',
            'manual'              => 'Manual Payments',
            'general'             => 'General Logs',
        );
    }
}

if (!function_exists('applog_channel_label')) {
    function applog_channel_label($channel)
    {
        $labels = applog_channels();
        return isset($labels[$channel]) ? $labels[$channel] : $channel;
    }
}

if (!function_exists('applog_dir')) {
    /**
     * <base>/<date>/<channel>, created if missing. Returns '' if it cannot be created.
     */
    function applog_dir($channel, $date = null)
    {
        $date    = ($date === null ? date('Y-m-d') : $date);
        $channel = trim(str_replace(array('..', '/', '\\'), '', $channel));
        $dir     = applog_base_dir() . '/' . $date . ($channel !== '' ? '/' . $channel : '');

        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        return is_dir($dir) ? $dir : '';
    }
}

if (!function_exists('applog_path')) {
    /**
     * Full path of a log file inside a channel. Default file name is <date>.txt.
     */
    function applog_path($channel, $file = null, $date = null)
    {
        $dir = applog_dir($channel, $date);
        if ($dir === '') {
            return '';
        }
        if ($file === null) {
            $file = ($date === null ? date('Y-m-d') : $date) . '.txt';
        }

        return $dir . '/' . basename($file);
    }
}

if (!function_exists('applog_source')) {
    /**
     * Human trigger label for the current request, derived from the route.
     *
     * The same model function is reached from the mobile app, the collection app, the
     * web portal and CRON services; this is what tells those apart in the log.
     */
    function applog_source()
    {
        static $source = null;
        if ($source !== null) {
            return $source;
        }

        if (php_sapi_name() === 'cli' || defined('STDIN')) {
            return $source = 'From CRON';
        }

        $class = strtolower(applog_route_class());

        $map = array(
            // Background / scheduled services
            'admin_services'           => 'From Service Trigger',
            'admin_services_27_10_25'  => 'From Service Trigger',
            'services'                 => 'From Service Trigger',
            'sync_erp_services'        => 'From Service Trigger',
            'intra_sync_services'      => 'From Service Trigger',
            'khimji_services'          => 'From Service Trigger',
            // Customer mobile app
            'mobile_api'               => 'From Mobile App',
            'mobileapi'                => 'From Mobile App',
            // Collection (field agent) app
            'adminapp_api'             => 'From Collection App',
            'admin_app_api'            => 'From Collection App',
            // Offline/branch sync APIs
            'sync_api'                 => 'From Sync API',
            'syncapi'                  => 'From Sync API',
            'sync_erp_api'             => 'From Sync API',
            'sktm_syncapi'             => 'From Sync API',
            'sync_wallet_api'          => 'From Sync API',
            // Customer web portal
            'user'                     => 'From Web Portal',
            'chitscheme'               => 'From Web Portal',
            'paymt'                    => 'From Web Portal',
            'digigold'                 => 'From Web Portal',
            'registration'             => 'From Web Portal',
            'cf_autodebit'             => 'From Cashfree Callback',
        );

        if (isset($map[$class])) {
            return $source = $map[$class];
        }
        if ($class !== '' && strpos($class, 'admin_') === 0) {
            return $source = 'From Admin Panel';
        }

        return $source = 'From Unknown Trigger';
    }
}

if (!function_exists('applog_route_class')) {
    /**
     * Controller class of the current request ('' when not resolvable).
     */
    function applog_route_class()
    {
        if (class_exists('CI_Controller', false) && function_exists('get_instance')) {
            $ci = get_instance();
            if (is_object($ci) && isset($ci->router) && is_object($ci->router)) {
                return (string) $ci->router->fetch_class();
            }
        }

        return '';
    }
}

if (!function_exists('applog_trigger_detail')) {
    /**
     * "controller::method" plus the request line, so the exact entry point is known.
     */
    function applog_trigger_detail()
    {
        $class  = applog_route_class();
        $method = '';

        if (class_exists('CI_Controller', false) && function_exists('get_instance')) {
            $ci = get_instance();
            if (is_object($ci) && isset($ci->router) && is_object($ci->router)) {
                $method = (string) $ci->router->fetch_method();
            }
        }

        $route = ($class !== '' ? $class . '::' . ($method !== '' ? $method : 'index') : 'cli');

        if (php_sapi_name() === 'cli') {
            return $route . ' (CLI)';
        }

        $verb = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '-';
        $uri  = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '-';

        return $route . ' (' . $verb . ' ' . $uri . ')';
    }
}

if (!function_exists('applog_client_ip')) {
    function applog_client_ip()
    {
        foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR') as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = explode(',', $_SERVER[$key]);
                return trim($ip[0]);
            }
        }

        return '-';
    }
}

if (!function_exists('applog_write')) {
    /**
     * Append one entry to a channel.
     *
     * @param string $channel Channel directory, e.g. 'transaction_entry'.
     * @param string $event   What happened, e.g. 'Inserted successfully'.
     * @param mixed  $data    Payload / query / response to record (encoded as JSON).
     * @param array  $opts    ref     => identifying keys of the record (array or string)
     *                        source  => override the trigger label
     *                        file    => log file name inside the channel
     *                        date    => log date folder (defaults to today)
     * @return bool TRUE when the entry was written.
     */
    function applog_write($channel, $event, $data = null, $opts = array())
    {
        $path = applog_path(
            $channel,
            (isset($opts['file']) ? $opts['file'] : null),
            (isset($opts['date']) ? $opts['date'] : null)
        );

        if ($path === '') {
            log_message('error', 'applog: unable to create log directory for channel ' . $channel);
            return false;
        }

        $source = (isset($opts['source']) && $opts['source'] !== '' ? $opts['source'] : applog_source());

        $entry  = "\n" . str_repeat('-', 90) . "\n";
        $entry .= '[' . date('Y-m-d H:i:s') . '] ' . applog_channel_label($channel) . ' | ' . $source . "\n";
        $entry .= ' Trigger : ' . applog_trigger_detail() . "\n";
        $entry .= ' Client  : ' . applog_client_ip() . "\n";
        $entry .= ' Event   : ' . $event . "\n";

        if (isset($opts['ref']) && $opts['ref'] !== '' && $opts['ref'] !== array()) {
            $entry .= ' Ref     : ' . applog_flatten($opts['ref']) . "\n";
        }
        if ($data !== null) {
            $entry .= ' Data    : ' . applog_encode($data) . "\n";
        }

        return (bool) @file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('applog_flatten')) {
    /**
     * "key=value | key=value" for the one-line Ref field.
     */
    function applog_flatten($ref)
    {
        if (!is_array($ref) && !is_object($ref)) {
            return (string) $ref;
        }

        $parts = array();
        foreach ((array) $ref as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $value = applog_encode($value);
            }
            $parts[] = $key . '=' . (($value === null || $value === '') ? '-' : $value);
        }

        return implode(' | ', $parts);
    }
}

if (!function_exists('applog_encode')) {
    function applog_encode($data)
    {
        if (is_string($data)) {
            return $data;
        }

        $json = json_encode($data);

        return ($json === false ? print_r($data, true) : $json);
    }
}
