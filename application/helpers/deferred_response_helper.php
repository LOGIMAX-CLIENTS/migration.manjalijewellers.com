<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Send the response now, keep running afterwards.
 *
 * A payment request ends with a pile of best-effort side work: the directAPI push
 * to the ERP, the SMS, the WhatsApp message, the receipt e-mail. None of it changes
 * what the caller is told -- the payment is already committed -- but all of it is
 * network I/O, so the app sits and waits for hosts that may be slow or unreachable.
 *
 * This flushes the redirect to the client and closes the connection, so the side
 * work runs on a request nobody is waiting on.
 *
 * The client is released either way: under php-fpm through fastcgi_finish_request(),
 * under mod_php through Content-Length + Connection: close. If a SAPI honours
 * neither, nothing breaks -- the request simply behaves as it did before.
 *
 * @param  string $location absolute URL to redirect the caller to ('' = just close)
 * @return void
 */
if ( ! function_exists('finish_response_and_continue')) {
    function finish_response_and_continue($location = '')
    {
        // The client is gone after this point; don't let its disconnect kill the
        // side work, and don't let a slow gateway trip max_execution_time either.
        @ignore_user_abort(TRUE);
        @set_time_limit(0);

        // Release the session file lock, otherwise the next request from the same
        // app blocks until this script ends -- exactly what we are trying to avoid.
        if (function_exists('session_write_close') && session_id() !== '') {
            @session_write_close();
        }

        // Drop anything buffered (paymt.php opens an ob_start() of its own).
        // The caller only needs the redirect header.
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        if ( ! headers_sent()) {
            if ($location !== '') {
                header('Location: '.$location, TRUE, 302);
            }
            header('Content-Length: 0');
            header('Connection: close');
        }

        @flush();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }
}

/* End of file deferred_response_helper.php */
/* Location: ./application/helpers/deferred_response_helper.php */
