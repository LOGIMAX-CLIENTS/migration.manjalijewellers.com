<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PanVerificationService
{
    private $CI;
    private $base_url;
    private $client_id;
    private $client_secret;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('cashfree', TRUE);

        $env = $this->CI->config->item('cashfree_env', 'cashfree');

        $this->base_url      = $this->CI->config->item('cashfree_' . $env . '_base_url', 'cashfree');
        $this->client_id     = $this->CI->config->item('cashfree_' . $env . '_client_id', 'cashfree');
        $this->client_secret = $this->CI->config->item('cashfree_' . $env . '_client_secret', 'cashfree');
    }

    public function verify_pan($pan, $name = '')
    {
        $pan = strtoupper(trim($pan));

        // 1. Server-side format validation
        if (!$this->validate_pan_format($pan)) {
            return [
                'status'          => false,
                'verified'        => false,
                'registered_name' => '',
                'message'         => 'Invalid PAN format',
                'from_cache'      => false
            ];
        }

        // 2. Check cache
        $cached = $this->get_cached_verification($pan);
        if ($cached !== false) {
            return $cached;
        }

        // 3. Call Cashfree API
        $api_result = $this->call_cashfree_api($pan, $name);

        // 4. Log the result
        $this->log_verification($pan, $api_result);

        return $api_result;
    }

    public function validate_pan_format($pan)
    {
        return (bool) preg_match('/^[A-Z]{5}\d{4}[A-Z]{1}$/', $pan);
    }
   
    private function get_cached_verification($pan)
    {
        $query = $this->CI->db->where('pan', $pan)
                               ->where('verified', 1)
                               ->get('ret_pan_verification_log');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return [
                'status'          => true,
                'verified'        => true,
                'registered_name' => $row->registered_name,
                'message'         => 'PAN verified successfully',
                'from_cache'      => true
            ];
        }

        return false;
    }

    private function call_cashfree_api($pan, $name = '')
    {
        $url = rtrim($this->base_url, '/') . '/pan';

        $payload = ['pan' => $pan];
        if (!empty($name)) {
            $payload['name'] = $name;
        }

        $headers = [
            'Content-Type: application/json',
            'x-client-id: ' . $this->client_id,
            'x-client-secret: ' . $this->client_secret
        ];

        $response = $this->execute_curl($url, $payload, $headers);

        if ($response['curl_error']) {
            $response = $this->execute_curl($url, $payload, $headers);

            if ($response['curl_error']) {
                return [
                    'status'          => false,
                    'verified'        => false,
                    'registered_name' => '',
                    'message'         => 'Network error, please try again',
                    'from_cache'      => false,
                    'api_error'       => $response['curl_error_msg']
                ];
            }
        }

        return $this->process_api_response($response);
    }

    private function execute_curl($url, $payload, $headers)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        $body      = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_errno($ch);
        $curl_msg  = curl_error($ch);

        curl_close($ch);

        return [
            'body'           => $body,
            'http_code'      => $http_code,
            'curl_error'     => ($curl_err !== 0),
            'curl_error_msg' => $curl_msg
        ];
    }

    private function process_api_response($response)
    {
        $http_code = $response['http_code'];
        $body      = $response['body'];
        $data      = json_decode($body, true);

        if ($http_code === 401) {
            log_message('error', 'Cashfree PAN API: Authentication failed. Response: ' . $body);
            return [
                'status'          => false,
                'verified'        => false,
                'registered_name' => '',
                'message'         => 'Verification service error',
                'from_cache'      => false,
                'api_error'       => 'Authentication failed (401)'
            ];
        }

        if ($http_code === 400) {
            log_message('error', 'Cashfree PAN API: Bad request. Response: ' . $body);
            $msg = isset($data['message']) ? $data['message'] : 'Invalid request';
            return [
                'status'          => false,
                'verified'        => false,
                'registered_name' => '',
                'message'         => $msg,
                'from_cache'      => false,
                'api_error'       => 'Bad request (400): ' . $body
            ];
        }

        if ($http_code >= 500) {
            log_message('error', 'Cashfree PAN API: Server error. Response: ' . $body);
            return [
                'status'          => false,
                'verified'        => false,
                'registered_name' => '',
                'message'         => 'Cashfree server error, try again later',
                'from_cache'      => false,
                'api_error'       => 'Server error (' . $http_code . '): ' . $body
            ];
        }

        if ($http_code === 0) {
            log_message('error', 'Cashfree PAN API: HTTP 0 - possible SSL/connection issue. Body: ' . $body);
            return [
                'status'          => false,
                'verified'        => false,
                'registered_name' => '',
                'message'         => 'Connection error, please try again',
                'from_cache'      => false,
                'api_error'       => 'HTTP 0 - connection failed'
            ];
        }

        if ($http_code === 403) {
            log_message('error', 'Cashfree PAN API: Forbidden (403). Response: ' . $body);
            return [
                'status'          => false,
                'verified'        => false,
                'registered_name' => '',
                'message'         => 'Verification service access denied',
                'from_cache'      => false,
                'api_error'       => 'Forbidden (403): ' . $body
            ];
        }

        if ($http_code === 409) {
            log_message('error', 'Cashfree PAN API: Conflict (409). Response: ' . $body);
            $msg = isset($data['message']) ? $data['message'] : 'Verification conflict, please retry';
            return [
                'status'          => false,
                'verified'        => false,
                'registered_name' => '',
                'message'         => $msg,
                'from_cache'      => false,
                'api_error'       => 'Conflict (409): ' . $body
            ];
        }

        if ($http_code === 422) {
            log_message('error', 'Cashfree PAN API: Unprocessable (422). Response: ' . $body);
            $msg = isset($data['message']) ? $data['message'] : 'Invalid PAN data';
            return [
                'status'          => false,
                'verified'        => false,
                'registered_name' => '',
                'message'         => $msg,
                'from_cache'      => false,
                'api_error'       => 'Unprocessable (422): ' . $body
            ];
        }

        if ($http_code === 200 && is_array($data)) {

            $is_valid        = isset($data['valid']) ? $data['valid'] : false;
            $registered_name = isset($data['registered_name']) ? $data['registered_name'] : '';
            $name_match      = isset($data['name_match_score']) ? $data['name_match_score'] : null;

            if ($is_valid) {
                return [
                    'status'           => true,
                    'verified'         => true,
                    'registered_name'  => $registered_name,
                    'name_match_score' => $name_match,
                    'message'          => 'PAN verified successfully',
                    'from_cache'       => false,
                    'api_response'     => $body
                ];
            } else {
                return [
                    'status'          => true,
                    'verified'        => false,
                    'registered_name' => '',
                    'message'         => 'PAN not found / invalid',
                    'from_cache'      => false,
                    'api_response'    => $body
                ];
            }
        }

        log_message('error', 'Cashfree PAN API: Unexpected response. HTTP Code: ' . $http_code . ' | Body: ' . $body);
        return [
            'status'          => false,
            'verified'        => false,
            'registered_name' => '',
            'message'         => 'Unexpected response from verification service (HTTP ' . $http_code . ')',
            'from_cache'      => false,
            'api_error'       => 'Unexpected (HTTP ' . $http_code . '): ' . $body
        ];
    }
    private function log_verification($pan, $result)
    {
        $data = [
            'pan'              => $pan,
            'registered_name'  => isset($result['registered_name']) ? $result['registered_name'] : null,
            'verified'         => (isset($result['verified']) && $result['verified']) ? 1 : 0,
            'name_match_score' => isset($result['name_match_score']) ? $result['name_match_score'] : null,
            'api_response'     => isset($result['api_response']) ? $result['api_response'] : null,
            'api_error'        => isset($result['api_error']) ? $result['api_error'] : null,
            'verified_at'      => date('Y-m-d H:i:s')
        ];

        $existing = $this->CI->db->where('pan', $pan)->get('ret_pan_verification_log');

        if ($existing->num_rows() > 0) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->CI->db->where('pan', $pan)->update('ret_pan_verification_log', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->CI->db->insert('ret_pan_verification_log', $data);
        }
    }
}
