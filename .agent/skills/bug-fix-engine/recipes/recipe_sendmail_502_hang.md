# Recipe: Sendmail Protocol 502 Hang

> Email sent via sendmail binary blocks PHP indefinitely on Linux, causing 502 Bad Gateway

## Metadata
- **Pattern ID**: PAT-TIMEOUT-002
- **Severity**: CRITICAL
- **Modules Affected**: email_model (shared by all modules using email), Account/Enrollment, Payment notifications
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal — any client using email_model with sendmail protocol on Linux hosting

## Created By
- **Developer**: Antigravity AI
- **Client**: nskjewels.com
- **Date**: 2026-04-07
- **Source Bug ID**: N/A

## Symptom
502 Bad Gateway error during operations that trigger email notifications (scheme enrollment, payment confirmations, account closing). The operation itself succeeds (data is saved to DB) but the HTTP response never returns. Works on localhost (Windows) but fails on live (Linux).

## Root Cause
`email_model::send_smtp_gmail()` uses `protocol => 'sendmail'` with `mailpath => '/usr/sbin/sendmail'`. The sendmail binary:
1. Runs **synchronously** inside the PHP process
2. Has **no timeout mechanism** — unlike SMTP which supports `smtp_timeout`
3. Can hang indefinitely if the mail queue is full, DNS resolution is slow, or the mail service is unresponsive

On **Windows localhost**, `/usr/sbin/sendmail` doesn't exist → CI email library silently fails → no hang.
On **Linux live server**, the binary IS present → blocks PHP → proxy timeout → **502 Bad Gateway**.

The critical issue is that email sending happens **inside the HTTP request** (after DB commit but before redirect), so a sendmail hang blocks the entire response.

## Detection
```command
grep -rn "'protocol'.*'sendmail'" admin/application/models/email_model.php
grep -rn "mailpath.*sendmail" admin/application/models/email_model.php
grep -rn "CURLOPT_TIMEOUT.*0" admin/application/models/sms_model.php
```

Also check notification wrapper functions have try-catch protection:
```command
grep -rn "function account_join_message\|function account_close_message\|function account_revert_message" admin/application/controllers/admin_manage.php
```

## Files
- `admin/application/models/email_model.php`
- `admin/application/controllers/admin_manage.php`
- `admin/application/models/sms_model.php`

## Fix

### Before (email_model.php — protocol config)
```php
$config = Array(
    'protocol'  => 'sendmail',
    'mailpath'      => "/usr/sbin/sendmail",
    'smtp_host' => $smtp_host,
    'smtp_port' => 465,
    'smtp_user' => ($server_type == 1 ? $smtp_user : $mail_server),
    'smtp_pass' => ($server_type == 1 ? $smtp_pass : $mail_password),
    'mailtype'  => 'html',    
    'charset'   => 'utf-8'
);
```

### After (email_model.php — SMTP with timeout)
```php
$config = Array(
    'protocol'  => 'smtp',
    'smtp_host' => ($server_type == 1 ? $smtp_host : 'localhost'),
    'smtp_port' => 465,
    'smtp_crypto' => 'ssl',
    'smtp_user' => ($server_type == 1 ? $smtp_user : $mail_server),
    'smtp_pass' => ($server_type == 1 ? $smtp_pass : $mail_password),
    'smtp_timeout' => 10,
    'mailtype'  => 'html',    
    'charset'   => 'utf-8'
);
```

### Before (admin_manage.php — account_join_message, no error handling)
```php
function account_join_message($id, $data) {
    $ser_model = self::SET_MODEL;
    // ... email/SMS logic with no protection ...
}
```

### After (admin_manage.php — try-catch wrapper)
```php
function account_join_message($id, $data) {
    try {
        $ser_model = self::SET_MODEL;
        // ... email/SMS logic ...
    } catch (\Exception $e) {
        log_message('error', 'account_join_message failed for id=' . $id . ': ' . $e->getMessage());
    } catch (\Throwable $e) {
        log_message('error', 'account_join_message fatal for id=' . $id . ': ' . $e->getMessage());
    }
}
```

### Before (sms_model.php — infinite cURL timeout)
```php
CURLOPT_TIMEOUT => 0,
```

### After (sms_model.php — 10 second timeout)
```php
CURLOPT_TIMEOUT => 10,
```

### Before (admin_manage.php — undefined $params in WhatsApp calls)
```php
$smsData = ["message" => $message, "template_name" => ..., "params" => $params];
```

### After (admin_manage.php — safe $params access)
```php
$smsData = ["message" => $message, "template_name" => ..., "params" => (isset($data['params']) ? $data['params'] : [])];
```

## Verification
1. Attempt scheme enrollment on the live server → should complete without 502
2. Check `application/logs/log-YYYY-MM-DD.php` — no fatal errors
3. Verify enrollment email is received (if SMTP config is correct)
4. If email fails, verify enrollment still succeeds (try-catch catches the error)
5. Check that notification failures are logged, not silently swallowed

## Notes
- The `account_close_message` and `account_revert_message` functions should also get the same try-catch treatment
- The `php_mail()` method in email_model.php uses `protocol => 'smtp'` with `smtp_host => 'localhost'` on port 25 — this also has no `smtp_timeout` and should be hardened
- Always check `serv_email`, `serv_sms`, `serv_whatsapp` settings when debugging notification-related 502s — if they're all 0, the notification path is eliminated
- Diagnostic technique: add step-by-step file logging with `memory_get_usage()` and `microtime()` to identify exact hang point in complex flows
