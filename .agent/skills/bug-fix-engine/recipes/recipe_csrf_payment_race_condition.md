# Recipe: CSRF Token Loss — Cookie Session Race Condition

> CI2 cookie-based sessions lose CSRF token data during concurrent AJAX requests on high-volume clients

## Metadata
- **Pattern ID**: PAT-CSRF-001
- **Severity**: CRITICAL
- **Modules Affected**: Payment (admin_payment), any module using `verify_form_secret()`
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL (but high-volume clients like NSK hit it more frequently)
- **Reason**: All clients use `sess_use_database = FALSE` (cookie sessions). Higher payment volume increases probability of concurrent AJAX triggering the race condition.

## Created By
- **Developer**: Antigravity AI
- **Client**: nskjewels.com
- **Date**: 2026-04-08 (updated from 2026-04-07 — original diagnosis was incorrect)
- **Source Bug ID**: N/A

## Symptom
"Invalid Form Submit" error appears intermittently on payment pages. Users see rejected payments despite having a valid session. Error frequency increases with payment volume. Diagnostic logs show:
```
FORM_SECRET_FAIL | Reason: TOKEN_NOT_FOUND | Pool size: 5 | Single: EMPTY
```

## Root Cause
**CI2 cookie-based session data loss during concurrent AJAX requests.**

All session data is stored in a browser cookie (`sess_use_database = FALSE`). Cookies have zero locking. When 2+ AJAX requests hit the server simultaneously:

1. Both read the SAME session cookie
2. Both modify session data independently (e.g., adding tokens, updating OTP, timestamps)
3. Both write back updated cookies in their responses
4. **Last response wins** — the other's changes are silently lost

The payment page workflow triggers multiple concurrent AJAX calls (account loading, metal rates, scheme details, OTP generation). If one of these AJAX responses overwrites the session cookie after `get_form_secret_key()` added a token, that token is lost. When `verify_form_secret()` checks the session, the token is gone.

### Why only high-volume clients see this
- More payments/day = more concurrent AJAX = higher probability of cookie overwrite
- More staff using simultaneously = more server load = slower responses = wider race windows
- More features enabled (OTP, WhatsApp, email) = more session writes per request

### Previous incorrect diagnosis (2026-04-07)
Previously attributed to "native form submit racing with AJAX". This was wrong — the payment flow uses ONLY AJAX via checkbox change handler → `insert_payment()` → `payment_success()` → `$.ajax`. The native form submit event is never the trigger for SaveAll.

## Detection
```command
# Check if client uses cookie sessions (vulnerable)
grep -n "sess_use_database" admin/application/config/config.php

# Check if form_secret is used in payment
grep -rn "verify_form_secret" admin/application/controllers/admin_payment.php

# Check if file backup exists (already fixed)
grep -n "_backup_token_to_file\|_verify_token_from_file" admin/application/helpers/formsecret_helper.php
```

If `sess_use_database = FALSE` and `verify_form_secret` exists but NO file backup functions → client is vulnerable.

## Files
- `admin/application/helpers/formsecret_helper.php` — main fix
- `admin/application/cache/form_tokens/` — directory for token backup files (must be created)

## Fix

### Replace entire `formsecret_helper.php` with:
```php
<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
function get_form_secret_key() {
  $CI =& get_instance();
  $token = md5(uniqid(rand(), true));
  $secrets = $CI->session->userdata('FORM_SECRETS');
  if (!is_array($secrets)) {
      $secrets = array();
  }
  // Pool limit: 30 tokens max
  if (count($secrets) >= 30) {
      array_shift($secrets);
  }
  $secrets[] = $token;
  $CI->session->set_userdata('FORM_SECRETS', $secrets);
  $CI->session->set_userdata('FORM_SECRET', $token);
  
  // FALLBACK: Server-side file backup keyed by USER ID (not session_id!)
  // session_id changes during CI2 regeneration, but uid never changes
  $uid = $CI->session->userdata('uid');
  if ($uid) {
      _backup_token_to_file($uid, $token);
  }
  
  return $token;
}

function verify_form_secret($token, $consume = true) {
    if (empty($token)) {
        _log_form_secret_failure('EMPTY_TOKEN', $token);
        return false;
    }
    $CI =& get_instance();
    $found = false;

    // Check 1: Multi-secrets pool in session
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
    
    // Check 2: Single FORM_SECRET (legacy)
    if (!$found) {
        $single_secret = $CI->session->userdata('FORM_SECRET');
        if ($single_secret && strcasecmp($token, $single_secret) === 0) {
            $found = true;
            if ($consume) {
                $CI->session->unset_userdata('FORM_SECRET');
            }
        }
    }
    
    // Check 3: Server-side file backup (survives session cookie loss)
    if (!$found) {
        $uid = $CI->session->userdata('uid');
        if ($uid) {
            $found = _verify_token_from_file($uid, $token, $consume);
            if ($found) {
                _log_form_secret_failure('RECOVERED_FROM_FILE_BACKUP', $token, $secrets);
            }
        }
    }
    
    if (!$found) {
        _log_form_secret_failure('TOKEN_NOT_FOUND', $token, $secrets, 
            isset($single_secret) ? $single_secret : null);
    }
    
    return $found;
}

function _backup_token_to_file($uid, $token) {
    $dir = APPPATH . 'cache/form_tokens';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    $file = $dir . '/user_' . intval($uid) . '.json';
    $tokens = array();
    if (file_exists($file)) {
        $content = @file_get_contents($file);
        $tokens = $content ? json_decode($content, true) : array();
        if (!is_array($tokens)) $tokens = array();
    }
    $tokens[] = array('token' => $token, 'time' => time());
    $cutoff = time() - 7200;
    $tokens = array_filter($tokens, function($t) use ($cutoff) {
        return isset($t['time']) && $t['time'] >= $cutoff;
    });
    $tokens = array_slice(array_values($tokens), -30);
    @file_put_contents($file, json_encode($tokens), LOCK_EX);
}

function _verify_token_from_file($uid, $token, $consume = true) {
    $dir = APPPATH . 'cache/form_tokens';
    $file = $dir . '/user_' . intval($uid) . '.json';
    if (!file_exists($file)) return false;
    
    $content = @file_get_contents($file);
    $tokens = $content ? json_decode($content, true) : array();
    if (!is_array($tokens)) return false;
    
    $cutoff = time() - 7200;
    foreach ($tokens as $i => $entry) {
        if (isset($entry['token']) && $entry['token'] === $token && $entry['time'] >= $cutoff) {
            if ($consume) {
                unset($tokens[$i]);
                @file_put_contents($file, json_encode(array_values($tokens)), LOCK_EX);
            }
            return true;
        }
    }
    return false;
}

function _log_form_secret_failure($reason, $token, $pool = null, $single = null) {
    $log_dir = 'log/' . date("Y-m-d");
    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0777, true);
    }
    $log_path = $log_dir . '/form_secret_fail_' . date("Y-m-d") . '.txt';
    $data  = "\n[" . date('Y-m-d H:i:s') . "] FORM_SECRET_FAIL";
    $data .= " | Reason: " . $reason;
    $data .= " | Token: " . substr($token, 0, 8) . '...';
    $data .= " | Pool size: " . (is_array($pool) ? count($pool) : 'N/A');
    $data .= " | Single: " . ($single ? substr($single, 0, 8) . '...' : 'EMPTY');
    $data .= " | URI: " . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'N/A');
    @file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
}

?>
```

### Post-fix: Create cache directory
```bash
mkdir -p admin/application/cache/form_tokens
chmod 777 admin/application/cache/form_tokens
```

## Verification
1. Deploy `formsecret_helper.php` + create cache directory
2. Make several test payments — no "Invalid Form Submit" should appear
3. Check `admin/log/YYYY-MM-DD/form_secret_fail_YYYY-MM-DD.txt`:
   - `RECOVERED_FROM_FILE_BACKUP` = fix is catching session loss ✅
   - No entries = no failures at all ✅
   - `TOKEN_NOT_FOUND` = fix not working, escalate to `sess_use_database = TRUE`
4. Check `admin/application/cache/form_tokens/` — should have `user_N.json` files

## Notes
- **Zero downtime**: No config changes needed. No user logout. Deploy and it works immediately.
- **Permanent fix**: Switch to `sess_use_database = TRUE` + create `ci_sessions` table. This eliminates ALL session race conditions but requires one-time logout of all users.
- **File cleanup**: Token files auto-expire (2hr TTL). For extra cleanliness, add cron: `find admin/application/cache/form_tokens -mmin +120 -delete`
- **Key insight**: File backup MUST be keyed by `uid` (user ID), NOT `session_id`. The session_id changes during CI2 regeneration, which is the exact event that causes the token loss.
- **Previous wrong fix (2026-04-07)**: `e.preventDefault()` on `#pay_form` submit handler was applied but was irrelevant — the payment flow never uses native form submit.
