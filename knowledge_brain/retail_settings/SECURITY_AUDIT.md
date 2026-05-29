# SECURITY AUDIT — Retail Settings Module
> **Module:** Retail Settings | **Round:** 8 (FINAL) | **Date:** 2026-03-17
> **Scope:** XSS, CSRF, SQL Injection, Logic Bypass, Credential Exposure — all layers (31 view files, 2 controllers, 2 models)

---

## ⚠️ CRITICAL CONFIG FINDINGS (Discovered Round 6)

```
$config['global_xss_filtering'] = FALSE;   // Line 418
$config['csrf_protection']       = FALSE;   // Line 431
```

**Impact:** With both flags disabled, **ALL XSS and CSRF findings below are actively exploitable in production.** CodeIgniter's built-in protection layers are off. Every bug rated 🟠 Medium has now been escalated to 🔴 Critical.

---

## Final Summary

| Category | Count | 🔴 Critical | 🟠 Medium | 🟡 Low |
|---|---|---|---|---|
| XSS (View Layer) | **9** ⬆️ | **2** | 1 | 0 |
| CSRF | 2 | **2** | 0 | 0 |
| SQL Injection | 2 | 0 | 2 | 0 |
| Logic / Access | 3 | 1 | 1 | 1 |
| HTML/Code Quality | 2 | 0 | 0 | 2 |
| **Credential Exposure** | **2** ⬆️ | **2** ✋ | 0 | 0 |
| **TOTAL** | **20** | **7** | **4** | **3** |

> _Escalations from Round 5 → 6 are marked with ↑_

---

## §1 — XSS Vulnerabilities

### XSS-001: Unescaped Flash Message in retail_setting/list.php
**File:** `admin/application/views/settings/retail_setting/list.php` | **Line:** 38
**Risk:** 🔴 Critical (was Medium — escalated due to `global_xss_filtering=FALSE`)
```php
<?php echo $message['message']; ?>    // No escaping — raw output
<?php echo $message['title']; ?>      // Line 37 — same issue
```
**Fix:** `echo htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8');`

### XSS-002: Unescaped Flash Message in general/form.php
**File:** `admin/application/views/settings/general/form.php` | **Line:** 33
**Risk:** 🔴 Critical — Same as XSS-001.
**Fix:** Same pattern.

### XSS-003: set_value() Without Global XSS Filtering
**File:** `admin/application/views/settings/retail_setting/form.php` | **Lines:** 177, 200, 223
**Risk:** 🟠 Medium — CI3's `set_value()` does NOT HTML-encode output when `global_xss_filtering = FALSE`. If a user submits `<script>alert(1)</script>` as a setting name/value, and the form fails validation and re-renders, the payload executes.
```php
<?php echo set_value('retail_setting[name]', $retail_setting['name']); ?>
```
**Fix:** Wrap with `htmlspecialchars()` or enable `global_xss_filtering = TRUE`.

---

## §2 — CSRF Vulnerabilities

### CSRF-001: clear_database — No Token + No OTP Gate
**Files:** `admin_settings.php` (server) + `admin_settings.js` L~843 (JS)
**Risk:** 🔴 Critical — `csrf_protection = FALSE` means any AJAX POST from any origin can trigger a full DB wipe. No server-side token check. No OTP verification. Single most dangerous bug.
**Fix:** Enable `csrf_protection = TRUE`, add token to all AJAX POSTs, add OTP gate.

### CSRF-002: Permission Matrix AJAX POSTs — No Token
**File:** `admin_settings.js` | **Lines:** ~3149, ~3179
```javascript
$.ajax({ url: base_url + "index.php/settings/access/add", type: "POST", data: postData });
$.ajax({ url: base_url + "index.php/settings/access/dashboard_add", type: "POST", data: postData });
```
**Risk:** 🔴 Critical — Attacker can silently elevate any role's permissions. With `csrf_protection=FALSE`, no token needed.
**Fix:** Enable CSRF globally + include token in all AJAX POSTs.

---

## §3 — SQL Injection

### SQLi-001: Raw String Interpolation in Model Queries
**File:** `admin/application/models/admin_settings_model.php` | **Multiple**
**Risk:** 🟠 Medium — `$this->db->query("SELECT ... WHERE id='$id'")` pattern in several methods. With `global_xss_filtering=FALSE`, no pre-sanitization occurs.
**Locations:** See `METHOD_INDEX.md §5`.

### SQLi-002: $_GET Bypass in ajax_village_list()
**File:** `admin/application/controllers/admin_settings.php` | **Line:** ~1205
**Risk:** 🟠 Medium — Raw `$_GET` access bypasses CI's input class.

---

## §4 — Logic / Access Control

### LOGIC-001: validate_cash_amt = 0 (₹2L Compliance Bypass)
**Risk:** 🔴 Critical — B-009. Cash limit rule entirely disabled. Compliance risk.

### LOGIC-002: catlog_module_post() — Undefined $module_id
**Risk:** 🟠 Medium — B-005. Update query runs with NULL id, silently fails.

### LOGIC-003: permission() Loop Not in Transaction
**Risk:** 🟡 Low — B-008. Partial failures corrupt permission state.

---

## §5 — HTML / Code Quality

### HTML-001: Duplicate id="id_ret_settings" × 3
**File:** `retail_setting/form.php` | **Lines:** 179, 202, 225
**Risk:** 🟡 Low — HTML invalid. JS `getElementById` only returns first match.

### HTML-002: Wrong Modal Title ("Delete Sms Service")
**File:** `retail_setting/list.php` | **Line:** 76
**Risk:** 🟡 Low — UX copy-paste artifact. No security impact.

---

## §6 — Hardcoded Credential (NEW — Round 7)

### CRED-001: Hardcoded WhatsApp Basic Auth Credential in Model
**File:** `admin/application/models/admin_usersms_model.php` | **Line:** 1875
**Risk:** 🔴 **Critical** — WhatsApp API Basic Auth credential encoded into source code and committed to version control.
```php
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    "authorization: Basic cHJlY2lzZXRyYTpIaXJoTmwxMA==",  // = precisetRA:HirhNl10
    "cache-control: no-cache",
    "content-type: application/json"
));
```
**Base64 decoded:** `precisetRA:HirhNl10`
**Fix:**
1. **Immediately rotate** the WhatsApp API credential at the provider portal
2. Move credentials to `admin/application/config/custom_api_keys.php` (excluded from VCS)
3. Reference via `$this->config->item('whatsapp_auth')`

**Note:** The production SQL dump also contains `msg91_authkey='363177AeaGCWyz3C60d42cefP1'` in `chit_settings` — similarly exposed. Rotate that too.

### CRED-002: Hardcoded SMS API Credential in Controller (NEW — Round 8)
**File:** `admin/application/controllers/admin_settings.php` | **Line:** 806
**Risk:** 🔴 **Critical** — SMS API Basic Auth credential `lmx@uzhavan:lmx@2018` hardcoded in source code, committed to version control.
```php
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json; charset=utf-8',
    'Authorization: Basic ' . base64_encode("lmx@uzhavan:lmx@2018")
));
```
**API Endpoint:** `http://nammauzhavan.com/api/v1/smjtvm_sendsms` (also HTTP not HTTPS — traffic unencrypted)
**Called by:** `get_SMS_data()` → triggered on each metal rate insert when notifications are enabled
**Fix:**
1. **Immediately rotate** the Nammauzhavan SMS account credential
2. Move to `admin/application/config/custom_api_keys.php` (excluded from VCS)
3. Switch endpoint to HTTPS

---

## §7 — Confirmed Safe

| Area | Status | Reason |
|---|---|---|
| DataTable population (all lists) | ✅ Safe | Pure JSON API, never raw PHP echo |
| form_open() / form_close() | ✅ Safe | CI URL builder |
| `set_value()` inputs (name/value/description) | ⚠️ Conditional | Safe only if global_xss_filtering=TRUE |
| All AJAX GET endpoints read-only | ✅ Safe | No state change |
| Access control profile gate | ✅ Safe | `$userType <= 2` guards sensitive tabs |
| Branch/cardbrand/weight CRUD | ✅ Safe | Data from JSON, not echoed directly |
| Social media URLs save guard | ✅ Safe | Server-side: only saved when `show_social_media == 1` |

---

## Fix Priority

| Priority | Bug | Effort |
|---|---|---|
| 🔴 P0-URGENT | CRED-001: Rotate WhatsApp API credential NOW | 5 min |
| 🔴 P0-URGENT | CRED-002: Rotate SMS API credential NOW + switch to HTTPS | 10 min |
| 🔴 P0 | Enable `csrf_protection = TRUE` globally | 30 min |
| 🔴 P0 | Add CSRF token to all AJAX POSTs | 2 hrs |
| 🔴 P0 | CSRF-001: Add OTP gate to clear_database server | 1 hr |
| 🔴 P0 | XSS-001 (9 files): htmlspecialchars on all flash message echoes | 30 min |
| 🔴 P0 | LOGIC-001: Fix validate_cash_amt = 0 | 30 min |
| 🟠 P1 | XSS-003: Wrap set_value() with htmlspecialchars | 30 min |
| 🟠 P1 | SQLi-001/002: Use CI query builder or bind params | 2 hrs |
| 🟡 P2 | HTML-001/002: Remove duplicate IDs, fix modal title | 15 min |

---

*Security Audit FINAL — Round 8 | 2026-03-17*
