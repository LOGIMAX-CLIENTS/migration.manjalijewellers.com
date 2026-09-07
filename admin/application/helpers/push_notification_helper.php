<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Push notification settings helper.
 *
 * Single read point for the push provider switch and its credentials, which live
 * in `chit_settings.notify_platform` / `chit_settings.notify_cred` (admin UI),
 * NOT in config.php -- file edits do not survive a deploy.
 *
 * notify_cred stores EVERY platform, so switching provider never destroys the
 * other provider's credentials:
 *   { "onesignal": {"id":"<app id>",    "key":"<rest api key>"},
 *     "fcm":       {"id":"<project id>","key":"<service account json>"} }
 *
 * Load it where it is used -- it is deliberately NOT in autoload.php:
 *   $this->load->helper('push_notification');
 */

if (!defined('NOTIFY_PLATFORM_ONESIGNAL')) define('NOTIFY_PLATFORM_ONESIGNAL', 1);
if (!defined('NOTIFY_PLATFORM_FCM'))       define('NOTIFY_PLATFORM_FCM', 2);

if (!function_exists('notify_platform_map')) {
    /**
     * The single source of truth for supported platforms.
     * A new platform = one more entry here. No schema change, no view edit.
     *
     * @return array id => array(slug, label, id_label, key_label, key_type)
     */
    function notify_platform_map()
    {
        return array(
            NOTIFY_PLATFORM_ONESIGNAL => array(
                'slug'      => 'onesignal',
                'label'     => 'One Signal',
                'id_label'  => 'App ID',
                'key_label' => 'REST API Key',
                'key_type'  => 'text',
                'hint'      => 'OneSignal Dashboard &rarr; Settings &rarr; Keys &amp; IDs',
            ),
            NOTIFY_PLATFORM_FCM => array(
                'slug'      => 'fcm',
                'label'     => 'FCM',
                'id_label'  => 'Project ID',
                'key_label' => 'Service Account JSON',
                'key_type'  => 'textarea',
                'hint'      => 'Firebase Console &rarr; Project Settings &rarr; Service accounts &rarr; Generate new private key',
            ),
        );
    }
}

if (!function_exists('notify_platform_slug')) {
    /**
     * Resolve a platform id to its slug. Unknown ids normalise to OneSignal and
     * are logged -- never silently route to a provider nobody selected.
     *
     * @param  int|string $platform_id
     * @return string
     */
    function notify_platform_slug($platform_id)
    {
        $map = notify_platform_map();
        $id  = (int) $platform_id;
        if (isset($map[$id])) {
            return $map[$id]['slug'];
        }
        log_message('error', 'push_notification: unknown notify_platform "' . $platform_id . '", falling back to One Signal');
        return $map[NOTIFY_PLATFORM_ONESIGNAL]['slug'];
    }
}

if (!function_exists('get_notify_settings')) {
    /**
     * Active platform + credentials. Static-cached per request.
     *
     * @param  bool $with_all_cred TRUE also returns every platform's credentials
     * @return array array(
     *                 'platform'      => (int) notify_platform,
     *                 'platform_slug' => (string),
     *                 'id'            => (string) active platform's id,
     *                 'key'           => (string) active platform's key,
     *                 'cred'          => (array)  all platforms [only when $with_all_cred]
     *              )
     */
    function get_notify_settings($with_all_cred = FALSE)
    {
        static $_notify_cache = NULL;

        if ($_notify_cache === NULL) {
            $CI = &get_instance();

            $platform = NOTIFY_PLATFORM_ONESIGNAL;
            $cred     = array();

            // A client that has not run the migration must log, not fatal.
            if (!$CI->db->field_exists('notify_cred', 'chit_settings') ||
                !$CI->db->field_exists('notify_platform', 'chit_settings')) {
                log_message('error', 'push_notification: chit_settings.notify_platform/notify_cred missing -- run migration 20260907_000001. Falling back to One Signal.');
            } else {
                $res = $CI->db->query("SELECT notify_platform, notify_cred FROM chit_settings LIMIT 1");
                if ($res && $res->num_rows() > 0) {
                    $row      = $res->row_array();
                    $platform = (int) $row['notify_platform'];
                    $decoded  = json_decode((string) $row['notify_cred'], TRUE);
                    if (is_array($decoded)) {
                        $cred = $decoded;
                    } elseif (trim((string) $row['notify_cred']) !== '') {
                        log_message('error', 'push_notification: notify_cred is not valid JSON -- treating as empty');
                    }
                }
            }

            $slug = notify_platform_slug($platform);
            $map  = notify_platform_map();
            // notify_platform_slug() already normalised an unknown id; keep them in step.
            foreach ($map as $pid => $meta) {
                if ($meta['slug'] === $slug) {
                    $platform = $pid;
                    break;
                }
            }

            $_notify_cache = array(
                'platform'      => $platform,
                'platform_slug' => $slug,
                'id'            => isset($cred[$slug]['id'])  ? (string) $cred[$slug]['id']  : '',
                'key'           => isset($cred[$slug]['key']) ? (string) $cred[$slug]['key'] : '',
                'cred'          => $cred,
            );
        }

        $out = $_notify_cache;
        if (!$with_all_cred) {
            unset($out['cred']);
        }
        return $out;
    }
}

if (!function_exists('notify_cred_for')) {
    /**
     * A SPECIFIC platform's credentials, regardless of which one is active.
     * The isolated _onesignal bodies use this so the settings screen is not decorative.
     *
     * @param  string $slug 'onesignal' | 'fcm'
     * @return array  array('id' => string, 'key' => string)
     */
    function notify_cred_for($slug)
    {
        $settings = get_notify_settings(TRUE);
        $cred     = isset($settings['cred'][$slug]) ? $settings['cred'][$slug] : array();
        return array(
            'id'  => isset($cred['id'])  ? (string) $cred['id']  : '',
            'key' => isset($cred['key']) ? (string) $cred['key'] : '',
        );
    }
}

if (!function_exists('notify_platform_is')) {
    /**
     * The switch every dispatcher calls.
     *
     * @param  string $slug
     * @return bool
     */
    function notify_platform_is($slug)
    {
        $settings = get_notify_settings();
        return ($settings['platform_slug'] === $slug);
    }
}

if (!function_exists('dedupe_device_tokens')) {
    /**
     * Collapse rows that share a device token and drop empty ones.
     *
     * registered_devices usually has no unique constraint, so one physical device
     * can sit on several rows and receive the same push twice.
     *
     * Dedupes by TOKEN VALUE, never by customer -- a customer's genuinely
     * different devices must all still be notified.
     *
     * @param  array  $rows Rows of arrays, or a flat list of token strings
     * @param  string $key  Column holding the token when $rows are arrays
     * @return array        Re-indexed, deduped
     */
    function dedupe_device_tokens($rows, $key = 'token')
    {
        if (empty($rows) || !is_array($rows)) {
            return array();
        }

        $seen = array();
        $out  = array();

        foreach ($rows as $row) {
            if (is_array($row)) {
                $token = isset($row[$key]) ? trim((string) $row[$key]) : '';
            } elseif (is_object($row)) {
                $token = isset($row->$key) ? trim((string) $row->$key) : '';
            } else {
                $token = trim((string) $row);
            }

            if ($token === '' || strtolower($token) === 'null') {
                continue;   // can never be delivered to
            }
            if (isset($seen[$token])) {
                continue;
            }

            $seen[$token] = TRUE;
            $out[]        = $row;
        }

        return $out;
    }
}

/* End of file push_notification_helper.php */
