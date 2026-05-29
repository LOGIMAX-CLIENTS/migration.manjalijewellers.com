<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
function get_form_secret_key() {
  $CI =& get_instance();
  $token = md5(uniqid(rand(), true));
  $secrets = $CI->session->userdata('FORM_SECRETS');
  if (!is_array($secrets)) {
      $secrets = array();
  }
  // Keep only the last 10 secrets to avoid session bloat
  if (count($secrets) >= 10) {
      array_shift($secrets);
  }
  $secrets[] = $token;
  $CI->session->set_userdata('FORM_SECRETS', $secrets);
  // For backward compatibility, also set the single FORM_SECRET
  $CI->session->set_userdata('FORM_SECRET', $token);
  return $token;
}

/**
 * Verifies if a token is valid and consumes it.
 * 
 * @param string $token The token to verify
 * @param bool $consume Whether to remove the token from the valid list after verification
 * @return bool True if valid, false otherwise
 */
function verify_form_secret($token, $consume = true) {
    if (empty($token)) return false;
    $CI =& get_instance();
    $found = false;

    // Check in multi-secrets pool
    $secrets = $CI->session->userdata('FORM_SECRETS');
    if (is_array($secrets)) {
        $key = array_search($token, $secrets);
        if ($key !== false) {
            $found = true;
            if ($consume) {
                unset($secrets[$key]);
                $CI->session->set_userdata('FORM_SECRETS', array_values($secrets));
            }
        }
    }
    
    // Check/consume in single FORM_SECRET for legacy support
    $single_secret = $CI->session->userdata('FORM_SECRET');
    if ($single_secret && strcasecmp($token, $single_secret) === 0) {
        $found = true;
        if ($consume) {
            $CI->session->unset_userdata('FORM_SECRET');
        }
    }
    
    return $found;
}

?>