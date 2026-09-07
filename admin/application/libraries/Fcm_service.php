<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Firebase Cloud Messaging -- HTTP v1 sender.
 *
 * Service-account OAuth2 (JWT signed with openssl_sign) -- zero composer deps.
 * The legacy server-key API is retired by Google, so there is no "Server Key"
 * field anywhere in this integration.
 *
 * Credentials come from chit_settings via get_notify_settings(), NOT config.php
 * and NOT a JSON file on disk -- a file never travels with a code deploy.
 *
 * Every public send method returns OneSignal-SHAPED JSON:
 *     {"id":"...", "recipients":N, "fcm_response":{...}}
 * so existing `json_decode($res)->recipients` call sites keep working unchanged.
 *
 * Usage:
 *     $this->load->library('fcm_service');
 *     $res = $this->fcm_service->sendToTokens($tokens, $title, $body, $data, $image);
 */
class Fcm_service
{
    /** Below any real FCM token (~142 chars), above every legacy OneSignal player id (36). */
    const MIN_FCM_TOKEN_LENGTH = 100;

    const CURL_CONNECT_TIMEOUT = 5;
    const CURL_TIMEOUT         = 15;
    const SEND_CONCURRENCY     = 20;

    /** Never write more than this many per-token failures to the log. */
    const MAX_LOGGED_FAILURES  = 5;

    const DEFAULT_OAUTH2_URL   = 'https://oauth2.googleapis.com/token';
    const DEFAULT_SEND_URL_BASE = 'https://fcm.googleapis.com/v1/projects/';

    const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    /** @var object CodeIgniter instance */
    private $CI;

    /** @var string Firebase project id */
    private $project_id = '';

    /** @var array Decoded service-account JSON */
    private $service_account = array();

    /** @var string|null Cached OAuth2 access token for this request */
    private $access_token = NULL;

    /** @var string Last error slug, '' when fine */
    private $last_error = '';

    public function __construct($params = array())
    {
        $this->CI = &get_instance();
        $this->CI->load->helper('push_notification');

        if (isset($params['project_id']) && isset($params['service_account_json'])) {
            $this->boot((string) $params['project_id'], (string) $params['service_account_json']);
        } else {
            $cred = notify_cred_for('fcm');
            $this->boot($cred['id'], $cred['key']);
        }
    }

    /**
     * @param string $project_id
     * @param string $service_account_json
     */
    private function boot($project_id, $service_account_json)
    {
        $this->project_id = trim((string) $project_id);

        $decoded = json_decode((string) $service_account_json, TRUE);
        if (is_array($decoded)) {
            $this->service_account = $decoded;
        }

        if ($this->project_id === '' || empty($this->service_account['private_key']) || empty($this->service_account['client_email'])) {
            $this->last_error = 'FCM_NOT_CONFIGURED';
            log_message('error', 'Fcm_service: not configured -- set Project ID and Service Account JSON under Settings > APP Notification.');
        }
    }

    // ---------------------------------------------------------------- public

    /**
     * Send to exactly one device token.
     *
     * @param  string $token
     * @param  string $title
     * @param  string $body
     * @param  array  $data  Custom data payload (values are cast to string by FCM v1)
     * @param  string $image Optional big picture URL
     * @return string JSON, OneSignal-shaped
     */
    public function sendToToken($token, $title, $body, $data = array(), $image = '')
    {
        return $this->sendToTokens(array($token), $title, $body, $data, $image);
    }

    /**
     * Send to a list of tokens. Dedupes, filters unusable tokens, sends in parallel.
     *
     * @param  array  $tokens Flat list of strings, or rows containing a 'token' key
     * @param  string $title
     * @param  string $body
     * @param  array  $data
     * @param  string $image
     * @return string JSON, OneSignal-shaped
     */
    public function sendToTokens($tokens, $title, $body, $data = array(), $image = '')
    {
        if ($this->last_error === 'FCM_NOT_CONFIGURED') {
            return $this->error_response('FCM_NOT_CONFIGURED', 'FCM credentials are not configured');
        }

        $tokens = $this->normalise_tokens($tokens);
        if (empty($tokens)) {
            return $this->shaped_response(0, array('skipped' => 'no usable tokens'));
        }

        $access_token = $this->get_access_token();
        if ($access_token === NULL) {
            return $this->error_response($this->last_error, 'Unable to obtain an FCM access token');
        }

        $url     = $this->send_url();
        $headers = array(
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json; charset=utf-8',
        );

        $delivered = 0;
        $failures  = array();
        $last_id   = '';
        $logged    = 0;

        // FCM v1 has no "send to many" -- one HTTPS round trip per token.
        // Sequential sends make a broadcast take minutes, so send in batches
        // of SEND_CONCURRENCY with curl_multi.
        foreach (array_chunk($tokens, self::SEND_CONCURRENCY) as $chunk) {
            $multi   = curl_multi_init();
            $handles = array();

            foreach ($chunk as $token) {
                $payload = json_encode(array('message' => $this->build_message($token, $title, $body, $data, $image)));
                $ch      = $this->new_handle($url, $headers, $payload);
                curl_multi_add_handle($multi, $ch);
                $handles[] = array('ch' => $ch, 'token' => $token);
            }

            $running = NULL;
            do {
                $status = curl_multi_exec($multi, $running);
                if ($running) {
                    curl_multi_select($multi, 1.0);
                }
            } while ($running && $status == CURLM_OK);

            foreach ($handles as $h) {
                $raw  = curl_multi_getcontent($h['ch']);
                $code = (int) curl_getinfo($h['ch'], CURLINFO_HTTP_CODE);
                $err  = curl_error($h['ch']);
                curl_multi_remove_handle($multi, $h['ch']);
                curl_close($h['ch']);

                if ($code === 200) {
                    $delivered++;
                    $decoded = json_decode((string) $raw, TRUE);
                    if (isset($decoded['name'])) {
                        $last_id = $decoded['name'];
                    }
                } else {
                    $reason = ($err !== '') ? $err : $this->extract_error($raw, $code);
                    if ($logged < self::MAX_LOGGED_FAILURES) {
                        log_message('error', 'Fcm_service: send failed (' . $code . ') for token ' . substr($h['token'], 0, 12) . '... : ' . $reason);
                        $logged++;
                    }
                    $failures[] = $reason;
                }
            }

            curl_multi_close($multi);
        }

        $failed = count($failures);
        if ($failed > self::MAX_LOGGED_FAILURES) {
            log_message('error', 'Fcm_service: ' . $failed . ' sends failed in total (' . self::MAX_LOGGED_FAILURES . ' logged above).');
        }

        $extra = array('attempted' => count($tokens), 'failed' => $failed);
        if ($delivered === 0 && $failed > 0) {
            $extra['error'] = reset($failures);
        }

        return $this->shaped_response($delivered, $extra, $last_id);
    }

    /**
     * Broadcast: query every notification-enabled device, delegate to sendToTokens().
     *
     * @param  string $title
     * @param  string $body
     * @param  array  $data
     * @param  string $image
     * @return string JSON, OneSignal-shaped
     */
    public function sendToAll($title, $body, $data = array(), $image = '')
    {
        $sql = "SELECT r.token AS token
                  FROM registered_devices r
                  INNER JOIN customer c ON (c.id_customer = r.id_customer)
                 WHERE c.notification = 1
                   AND c.active = 1
                   AND r.token IS NOT NULL
                   AND r.token <> ''
                   AND CHAR_LENGTH(r.token) >= " . (int) self::MIN_FCM_TOKEN_LENGTH;

        $res  = $this->CI->db->query($sql);
        $rows = ($res) ? $res->result_array() : array();

        if (empty($rows)) {
            // Say WHICH precondition failed -- "0 recipients" alone sends people
            // hunting for a credentials problem that isn't there.
            $diag = $this->CI->db->query(
                "SELECT COUNT(*) AS devices,
                        SUM(r.token IS NOT NULL AND r.token <> '') AS with_token,
                        SUM(CHAR_LENGTH(r.token) >= " . (int) self::MIN_FCM_TOKEN_LENGTH . ") AS fcm_sized,
                        SUM(c.notification = 1) AS opted_in,
                        SUM(c.active = 1) AS active_cus
                   FROM registered_devices r
                   LEFT JOIN customer c ON (c.id_customer = r.id_customer)"
            );
            $d = ($diag && $diag->num_rows() > 0) ? $diag->row_array() : array();
            log_message('error', 'Fcm_service: broadcast found no usable FCM tokens. registered_devices=' .
                (isset($d['devices']) ? $d['devices'] : '?') . ', with_token=' .
                (isset($d['with_token']) ? (int) $d['with_token'] : '?') . ', fcm_sized(>=' . self::MIN_FCM_TOKEN_LENGTH . ')=' .
                (isset($d['fcm_sized']) ? (int) $d['fcm_sized'] : '?') . ', notification=1 -> ' .
                (isset($d['opted_in']) ? (int) $d['opted_in'] : '?') . ', active=1 -> ' .
                (isset($d['active_cus']) ? (int) $d['active_cus'] : '?'));
        }

        return $this->sendToTokens($rows, $title, $body, $data, $image);
    }

    /**
     * Credential probe -- obtains an access token without delivering anything.
     * Used by the settings screen / verification scripts.
     *
     * @return array array('status' => bool, 'message' => string)
     */
    public function verify_credentials()
    {
        if ($this->last_error === 'FCM_NOT_CONFIGURED') {
            return array('status' => FALSE, 'message' => 'FCM credentials are not configured');
        }
        $token = $this->get_access_token();
        if ($token === NULL) {
            return array('status' => FALSE, 'message' => 'OAuth2 token request failed (' . $this->last_error . ')');
        }
        return array('status' => TRUE, 'message' => 'Credentials accepted by Google OAuth2');
    }

    // --------------------------------------------------------------- private

    /**
     * Flatten rows to strings, drop duplicates, drop anything that cannot be a
     * real FCM token. Filtering here is not cosmetic: every unusable token
     * otherwise costs a full HTTPS round trip that is guaranteed to fail.
     *
     * @param  array $tokens
     * @return array
     */
    private function normalise_tokens($tokens)
    {
        if (!is_array($tokens)) {
            $tokens = array($tokens);
        }

        $flat = array();
        foreach ($tokens as $row) {
            if (is_array($row)) {
                $flat[] = isset($row['token']) ? $row['token'] : '';
            } elseif (is_object($row)) {
                $flat[] = isset($row->token) ? $row->token : '';
            } else {
                $flat[] = $row;
            }
        }

        $flat = dedupe_device_tokens($flat);

        $usable  = array();
        $dropped = 0;
        foreach ($flat as $token) {
            $token = trim((string) $token);
            if (strlen($token) < self::MIN_FCM_TOKEN_LENGTH) {
                $dropped++;    // legacy OneSignal player id or a truncated token
                continue;
            }
            $usable[] = $token;
        }

        if ($dropped > 0) {
            log_message('error', 'Fcm_service: dropped ' . $dropped . ' token(s) shorter than ' . self::MIN_FCM_TOKEN_LENGTH . ' chars (not FCM tokens).');
        }

        return $usable;
    }

    /**
     * @param  string $token
     * @param  string $title
     * @param  string $body
     * @param  array  $data
     * @param  string $image
     * @return array FCM v1 `message` object
     */
    private function build_message($token, $title, $body, $data, $image)
    {
        $notification = array(
            'title' => (string) $title,
            'body'  => (string) $body,
        );

        $image = trim((string) $image);
        if ($image !== '') {
            $notification['image'] = $image;
        }

        // FCM v1 requires every data value to be a string.
        $string_data = array();
        foreach ((array) $data as $k => $v) {
            if (is_array($v) || is_object($v)) {
                $v = json_encode($v);
            }
            $string_data[(string) $k] = (string) $v;
        }

        $message = array(
            'token'        => $token,
            'notification' => $notification,
            'data'         => $string_data,
            'android'      => array('priority' => 'high'),
            'apns'         => array(
                'headers' => array('apns-priority' => '10'),
                'payload' => array('aps' => array('sound' => 'default')),
            ),
        );

        return $message;
    }

    /**
     * Signed-JWT -> OAuth2 access token. Cached for this request only.
     *
     * @return string|null
     */
    private function get_access_token()
    {
        if ($this->access_token !== NULL) {
            return $this->access_token;
        }

        $now    = time();
        $header = array('alg' => 'RS256', 'typ' => 'JWT');
        $claim  = array(
            'iss'   => $this->service_account['client_email'],
            'scope' => self::SCOPE,
            'aud'   => $this->oauth2_url(),
            'iat'   => $now,
            'exp'   => $now + 3600,
        );

        $signing_input = $this->b64url(json_encode($header)) . '.' . $this->b64url(json_encode($claim));

        $signature = '';
        $key       = openssl_pkey_get_private($this->service_account['private_key']);
        if ($key === FALSE) {
            $this->last_error = 'FCM_BAD_PRIVATE_KEY';
            log_message('error', 'Fcm_service: private_key in the service account JSON could not be parsed.');
            return NULL;
        }

        $signed = openssl_sign($signing_input, $signature, $key, 'sha256WithRSAEncryption');
        if (function_exists('openssl_pkey_free')) {
            @openssl_pkey_free($key);
        }
        if (!$signed) {
            $this->last_error = 'FCM_JWT_SIGN_FAILED';
            log_message('error', 'Fcm_service: openssl_sign() failed.');
            return NULL;
        }

        $jwt = $signing_input . '.' . $this->b64url($signature);

        $post = http_build_query(array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ));

        $ch = $this->new_handle($this->oauth2_url(), array('Content-Type: application/x-www-form-urlencoded'), $post);
        $raw  = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($code !== 200) {
            $this->last_error = 'FCM_AUTH_FAILED';
            log_message('error', 'Fcm_service: OAuth2 token request failed (' . $code . ') ' . (($err !== '') ? $err : (string) $raw));
            return NULL;
        }

        $decoded = json_decode((string) $raw, TRUE);
        if (empty($decoded['access_token'])) {
            $this->last_error = 'FCM_AUTH_FAILED';
            log_message('error', 'Fcm_service: OAuth2 response contained no access_token.');
            return NULL;
        }

        $this->access_token = $decoded['access_token'];
        $this->last_error   = '';
        return $this->access_token;
    }

    /**
     * A cURL handle with timeouts already set. Without them one stalled
     * connection hangs the page forever.
     *
     * @param  string $url
     * @param  array  $headers
     * @param  string $post_body
     * @return resource
     */
    private function new_handle($url, $headers, $post_body)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CURL_CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);
        return $ch;
    }

    /** @return string */
    private function oauth2_url()
    {
        $override = $this->CI->config->item('fcm_oauth2_url');
        return (!empty($override)) ? $override : self::DEFAULT_OAUTH2_URL;
    }

    /** @return string */
    private function send_url()
    {
        $base = $this->CI->config->item('fcm_send_url_base');
        if (empty($base)) {
            $base = self::DEFAULT_SEND_URL_BASE;
        }
        return rtrim($base, '/') . '/' . $this->project_id . '/messages:send';
    }

    /**
     * @param  string $raw
     * @param  int    $code
     * @return string
     */
    private function extract_error($raw, $code)
    {
        $decoded = json_decode((string) $raw, TRUE);
        if (isset($decoded['error']['status'])) {
            return $decoded['error']['status'];
        }
        if (isset($decoded['error']['message'])) {
            return $decoded['error']['message'];
        }
        return 'HTTP ' . $code;
    }

    /**
     * OneSignal-shaped success/partial response.
     *
     * @param  int    $recipients
     * @param  array  $extra
     * @param  string $id
     * @return string JSON
     */
    private function shaped_response($recipients, $extra = array(), $id = '')
    {
        return json_encode(array(
            'id'           => $id,
            'recipients'   => (int) $recipients,
            'fcm_response' => $extra,
        ));
    }

    /**
     * @param  string $slug
     * @param  string $message
     * @return string JSON
     */
    private function error_response($slug, $message)
    {
        return json_encode(array(
            'id'           => '',
            'recipients'   => 0,
            'error'        => ($slug !== '') ? $slug : 'FCM_ERROR',
            'errors'       => array($message),
            'fcm_response' => array('error' => ($slug !== '') ? $slug : 'FCM_ERROR', 'message' => $message),
        ));
    }

    /**
     * @param  string $data
     * @return string
     */
    private function b64url($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

/* End of file Fcm_service.php */
