# Recipe: 502 Bad Gateway — PHP-FPM Missing request_terminate_timeout

> PHP-FPM has no request kill timeout, so any slow request (email, API, heavy query) hangs until Cloudflare returns 502

## Metadata
- **Pattern ID**: PAT-INFRA-001
- **Severity**: CRITICAL
- **Modules Affected**: ALL (server-level, affects every page)
- **Auto-fixable**: Yes (server config change, no code change)
- **Related**: PAT-TIMEOUT-002 (sendmail hang), PAT-SESS-002 (AJAX session race)

## Client Scope
- **Applies to**: ALL (any client on Linux with PHP-FPM + Cloudflare)
- **Reason**: Universal — default PHP-FPM config has `request_terminate_timeout = 0` (disabled), meaning any slow request can hang forever

## Created By
- **Developer**: Antigravity AI
- **Client**: nskjewels.com
- **Date**: 2026-04-22
- **Source Bug ID**: N/A (recurring 502 fixed 3-4 times at code level, root cause was server config)

## Symptom
Intermittent 502 Bad Gateway from Cloudflare. The Cloudflare error page shows:
- Browser: ✅ Working
- Cloudflare: ✅ Working  
- Host: ❌ Error

The 502 recurs even after fixing multiple code-level causes (sendmail hang, AJAX race condition, heavy queries, cron worker lock). Each code fix reduces frequency but never eliminates the 502 completely.

## Root Cause
**PHP-FPM's `request_terminate_timeout` defaults to `0` (disabled).** This means:

1. Any PHP request can run indefinitely — no kill timeout
2. When a request hangs (email send, external API call, slow query, cURL to payment gateway), the PHP worker is occupied forever
3. Cloudflare has a fixed 100-second proxy timeout for free/pro plans
4. After 100 seconds with no response, Cloudflare returns **502 Bad Gateway**
5. The PHP worker may still be alive and occupied even after Cloudflare has returned the 502

### Why code-level fixes don't permanently solve it
Code fixes (sendmail→SMTP, AJAX debounce, query optimization, cron batching) reduce the number of requests that CAN hang, but they can never eliminate ALL possible slow paths. A single uncovered slow path causes the 502 to recur. The `request_terminate_timeout` is the **safety net** that prevents ANY slow request from causing a 502.

### Diagnostic evidence
PHP-FPM log shows:
```
WARNING: [pool www] server reached pm.max_children setting (50), consider raising it
WARNING: [pool www] seems busy, spawning N children, there are 0 idle
```
But on days when `pm.max_children` is NOT reached, the 502 still occurs — proving it's a single-request hang, not worker exhaustion.

## Detection
```bash
# Check 1: Is request_terminate_timeout set?
sudo grep -E "request_terminate_timeout" /etc/php/*/fpm/pool.d/www.conf
# VULNERABLE if: value is 0 or line is commented out (;request_terminate_timeout = 0)

# Check 2: Is slow log enabled?
sudo grep -E "request_slowlog_timeout|slowlog" /etc/php/*/fpm/pool.d/www.conf
# VULNERABLE if: request_slowlog_timeout is 0 or commented out

# Check 3: Is pm.max_requests set? (memory leak prevention)
sudo grep -E "pm.max_requests" /etc/php/*/fpm/pool.d/www.conf
# VULNERABLE if: value is 0 (workers never recycle)

# Check 4: Recent FPM warnings
sudo grep -E "WARNING|ALERT|max_children" /var/log/php*-fpm.log | tail -20
```

**Vulnerable if**: `request_terminate_timeout` is `0` or commented out.

## Files
- `/etc/php/7.4/fpm/pool.d/www.conf` (or equivalent PHP version path)
- No application code changes needed

## Fix

### Fix 1: Set request_terminate_timeout (THE critical fix)

#### Before
```ini
; Default Value: 0
;request_terminate_timeout = 0
```

#### After
```ini
; Kill any request that runs longer than 90 seconds
; This MUST be less than Cloudflare's 100s timeout to prevent 502
request_terminate_timeout = 90s
```

**Why 90 seconds**: Cloudflare free/pro timeout is 100 seconds. Setting PHP-FPM to kill at 90s means the user gets a proper PHP error (504 or white page with error log) instead of a Cloudflare 502. This gives 10 seconds of buffer.

### Fix 2: Enable slow log (diagnostic)

#### Before
```ini
slowlog = log/$pool.log.slow
;request_slowlog_timeout = 0
```

#### After
```ini
slowlog = /var/log/php-fpm-slow.log
request_slowlog_timeout = 10s
```

**Why 10 seconds**: Any PHP request taking >10 seconds is abnormal for a web app. The slow log captures the exact PHP function stack trace at that moment, identifying the root cause for targeted code fixes.

### Fix 3: Set pm.max_requests (memory leak prevention)

#### Before
```ini
;pm.max_requests = 0
```

#### After
```ini
pm.max_requests = 500
```

**Why 500**: Workers recycle after 500 requests, preventing gradual memory bloat from leaky PHP code. Low enough to prevent leaks, high enough to avoid constant respawning overhead.

### Apply
```bash
sudo nano /etc/php/7.4/fpm/pool.d/www.conf
# Make the 3 changes above
sudo systemctl restart php7.4-fpm
sudo systemctl status php7.4-fpm
```

## Verification
1. **Immediate**: `sudo systemctl status php7.4-fpm` shows `active (running)`
2. **Slow log test**: Hit a known slow page, then check `sudo cat /var/log/php-fpm-slow.log` — should show stack traces for requests >10s
3. **502 elimination**: Monitor for 48 hours — 502 should not recur
4. **If 502 still occurs**: Check Cloudflare settings for a lower custom timeout. Also verify Apache `ProxyTimeout` is ≥90s
5. **Ongoing monitoring**: Periodically check slow log for recurring slow functions and fix them at code level

## Recommended pm.* Settings (Reference)
For a jewelry ERP with 50-100 concurrent admin users:
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 15
pm.min_spare_servers = 12
pm.max_spare_servers = 30
pm.max_requests = 500
request_terminate_timeout = 90s
request_slowlog_timeout = 10s
slowlog = /var/log/php-fpm-slow.log
```

## Notes
- **This recipe is the SAFETY NET**. Code-level recipes (PAT-TIMEOUT-002 sendmail, PAT-SESS-002 AJAX race) fix specific hang causes. This recipe prevents ANY unfixed hang from causing a 502.
- **Always apply this recipe FIRST** when onboarding a new client on Linux + Cloudflare. It takes 2 minutes and prevents the most common 502 pattern.
- **Cloudflare Enterprise** plans allow configuring proxy timeout >100s. For free/pro plans, 100s is fixed.
- **The slow log is your best friend** for diagnosing recurring performance issues. Check it weekly.
- **`request_terminate_timeout` does NOT affect CLI scripts** — only requests coming through PHP-FPM. Cron jobs run via CLI are unaffected.
- **Memory**: At 3.8GB RAM with 50 workers, each worker averages ~30-50MB. This is healthy. If workers grow beyond 80MB each, investigate memory leaks.
- **IMPORTANT**: This recipe is the safety net. For the actual slow query fixes, also apply: PAT-QUERY-002 (dashboard reorder N+1 getTagging loop) and PAT-QUERY-003 (Cashfree cron missing ref_trans_id index). All three together eliminated 86 slow requests/day on nskjewels.com.
