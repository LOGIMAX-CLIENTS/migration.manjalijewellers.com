<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Circuit breaker for the directAPI (ERP) push.
 *
 * directAPI is a best-effort, fire-and-forget sync: every record is written to the
 * intermediate tables (customer_reg / transaction) before the push, and anything the
 * push does not deliver is transferred later by the sync tool. So when the ERP host is
 * unreachable there is nothing to gain by waiting for it -- and a lot to lose, because
 * the push runs inside the payment request (and inside an open DB transaction).
 *
 * These functions remember a connection-level failure and let callers skip the cURL
 * call entirely for the next `directAPI_down_cooldown` seconds, so only the first
 * request after the host goes down pays the connect timeout.
 *
 * Only connection failures (resolve / connect / timeout) trip the breaker. An HTTP
 * error response means the host is up and is left alone.
 */

if ( ! function_exists('directapi_state_file')) {
    function directapi_state_file()
    {
        return APPPATH.'cache/directapi_down.txt';
    }
}

if ( ! function_exists('directapi_config')) {
    /**
     * Read a directAPI tuning value from config.php, falling back to a safe default.
     *
     * config.php is per-deployment and is not in version control, so these keys are
     * not guaranteed to exist. Never pass config->item() straight to cURL: a missing
     * key returns FALSE, and (int)FALSE = 0, which cURL reads as "no limit".
     *
     * @param  string $key
     * @param  int    $default
     * @return int
     */
    function directapi_config($key, $default)
    {
        $CI  = &get_instance();
        $val = $CI->config->item($key);

        if ($val === NULL || $val === FALSE || $val === '') {
            return (int) $default;
        }

        return (int) $val;
    }
}

if ( ! function_exists('directapi_is_down')) {
    /**
     * @return bool TRUE when a connection failure was recorded inside the cooldown window.
     */
    function directapi_is_down()
    {
        $cooldown = directapi_config('directAPI_down_cooldown', 60);
        if ($cooldown <= 0) {
            return FALSE;
        }

        $file = directapi_state_file();
        if ( ! is_file($file)) {
            return FALSE;
        }

        $failed_at = (int) @file_get_contents($file);
        if ((time() - $failed_at) >= $cooldown) {
            @unlink($file);   // cooldown expired - let the next call probe the host again
            return FALSE;
        }

        return TRUE;
    }
}

if ( ! function_exists('directapi_is_connection_error')) {
    /**
     * Did the call fail because the host could not be reached, rather than because
     * the host answered with something we did not like?
     *
     * @param  int $errno value of curl_errno()
     * @return bool
     */
    function directapi_is_connection_error($errno)
    {
        return in_array((int) $errno, array(
            6,   // CURLE_COULDNT_RESOLVE_HOST
            7,   // CURLE_COULDNT_CONNECT
            28,  // CURLE_OPERATION_TIMEDOUT
            35,  // CURLE_SSL_CONNECT_ERROR
            56,  // CURLE_RECV_ERROR
        ), TRUE);
    }
}

if ( ! function_exists('directapi_mark_down')) {
    /**
     * Record a connection failure and start the cooldown.
     *
     * @param string $reason cURL error text, for the log
     */
    function directapi_mark_down($reason = '')
    {
        @file_put_contents(directapi_state_file(), time(), LOCK_EX);
        log_message('error', 'directAPI unreachable, skipping pushes for the cooldown period: '.$reason);
    }
}

if ( ! function_exists('directapi_mark_up')) {
    /**
     * Clear the breaker after a call that actually reached the host.
     */
    function directapi_mark_up()
    {
        $file = directapi_state_file();
        if (is_file($file)) {
            @unlink($file);
        }
    }
}

/* End of file directapi_helper.php */
/* Location: ./application/helpers/directapi_helper.php */
