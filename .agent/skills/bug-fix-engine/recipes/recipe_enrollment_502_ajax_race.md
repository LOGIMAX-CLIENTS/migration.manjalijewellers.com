# Recipe: 502 Bad Gateway During Account Enrollment — AJAX Session Race

> Referral code keyup AJAX fires on every keystroke without debounce/abort, corrupting CI2 cookie session and causing 502 on enrollment redirect

## Metadata
- **Pattern ID**: PAT-SESS-002
- **Severity**: CRITICAL
- **Modules Affected**: Account Enrollment (admin_manage), Payment (payment_model)
- **Auto-fixable**: Yes
- **Related**: PAT-CSRF-001 (same root cause — CI2 cookie session race)

## Client Scope
- **Applies to**: ALL (any client using CI2 cookie sessions + referral code feature)
- **Reason**: All clients use `sess_use_database = FALSE`. The referral code keyup AJAX is present in all deployments via `scheme_account.js`.

## Created By
- **Developer**: Antigravity AI
- **Client**: nskjewels.com
- **Date**: 2026-04-09
- **Source Bug ID**: N/A

## Symptom
Users intermittently get **502 Bad Gateway** when submitting the account enrollment form (`admin_manage/account_post`). The error is non-deterministic — some enrollments succeed, others fail. Happens more when referral codes are typed quickly.

## Root Cause
**CI2 cookie session corruption caused by un-debounced AJAX on referral code input.**

1. `#referal_code` keyup fires `$.ajax()` POST to `admin_manage/referralcode_check` on **every keystroke** — no debounce, no abort
2. Typing "ABC123" generates **6 concurrent requests**
3. CI2 cookie sessions regenerate the session ID on each request (`sess_time_to_update`)
4. Each AJAX response writes a different `Set-Cookie` header back to the browser
5. The browser accepts the **last response's cookie**, silently discarding all others
6. The session data from earlier responses is **permanently lost**
7. When the enrollment form POST arrives, the session is corrupted/stale
8. Controller processes with NULL/missing session data → PHP-FPM crashes or sends malformed response
9. Nginx interprets the upstream failure as **502 Bad Gateway**

### Secondary amplifier
Even when the session survives, uninitialized PHP variables in `admin_manage.php` and `payment_model.php` generate `Undefined variable/index` notices. While `display_errors = Off` prevents direct output leakage, these notices indicate fragile code paths that fail unpredictably with corrupted session data.

## Detection
```command
# Check 1: Un-debounced referral code AJAX (primary bug)
grep -n "referal_code.*keyup" admin/assets/js/scheme_account.js

# Check 2: No abort logic for referral XHR
grep -n "refCodeXhr\|abort" admin/assets/js/scheme_account.js

# Check 3: Uninitialized variables in payment model
grep -n "allow_advancePay\|allow_pendingPay\|eligible_wgt\|dg_benefit_value" admin/application/models/payment_model.php | head -20

# Check 4: Missing isset guards in enrollment controller
grep -n "is_opening\|cus_single\|emp_single" admin/application/controllers/admin_manage.php

# Check 5: Cookie sessions (vulnerable to race)
grep -n "sess_use_database" admin/application/config/config.php
```

**Vulnerable if**: referral_code keyup has NO `setTimeout`/debounce AND no `abort()` call.

## Files
- `admin/assets/js/scheme_account.js` — **primary fix** (debounce + abort)
- `admin/application/controllers/admin_manage.php` — variable init + isset guards
- `admin/application/models/payment_model.php` — variable init + isset guards
- `admin/application/models/admin_usersms_model.php` — $mobile init

## Fix

### Fix 1: Debounce + Abort referral code AJAX (scheme_account.js)

#### Before
```javascript
$("#referal_code").on("keyup", function(event) {
    if ($("#referal_code").val() != "") {
        event.preventDefault();
        var codes = $("#referal_code").val();
        if (codes != referalcode) {
            checkreferalcode(codes);
        }
    } else {
        $("#ref_name").text("");
        $('#submit').prop('disabled', false);
        $("#referal_code").val("");
    }
});

function checkreferalcode(codes) {
    var id_customer = $("#id_customer").val();
    $('.overlay').css('display', 'block');
    $.ajax({
        type: 'POST',
        data: { 'referal_code': codes, 'id_customer': id_customer },
        url: base_url + 'index.php/admin_manage/referralcode_check',
        success: function (data) {
            $('.overlay').css('display', 'none');
            // ... success handling ...
        },
    });
}
```

#### After
```javascript
var refCodeTimer = null;
var refCodeXhr = null;
$("#referal_code").on("keyup", function (event) {
    if ($("#referal_code").val() != "") {
        event.preventDefault();
        var codes = $("#referal_code").val();
        if (codes != referalcode) {
            clearTimeout(refCodeTimer);
            refCodeTimer = setTimeout(function () {
                checkreferalcode(codes);
            }, 500);
        }
    } else {
        clearTimeout(refCodeTimer);
        $("#ref_name").text("");
        $('#submit').prop('disabled', false);
        $("#referal_code").val("");
    }
});

function checkreferalcode(codes) {
    var id_customer = $("#id_customer").val();
    if (refCodeXhr) { refCodeXhr.abort(); }
    $('.overlay').css('display', 'block');
    refCodeXhr = $.ajax({
        type: 'POST',
        data: { 'referal_code': codes, 'id_customer': id_customer },
        url: base_url + 'index.php/admin_manage/referralcode_check',
        success: function (data) {
            refCodeXhr = null;
            $('.overlay').css('display', 'none');
            // ... success handling unchanged ...
        },
        error: function (xhr, status, error) {
            refCodeXhr = null;
            if (status !== 'abort') {
                $('.overlay').css('display', 'none');
            }
        },
    });
}
```

### Fix 2: Initialize variables in controller (admin_manage.php)

Search for the `account_post` function. Find where `$cus_single` and `$emp_single` are used.

#### Before
```php
// Variables used without initialization
$data['referal_code_cus'] = $cus_single;
$data['referal_code_emp'] = $emp_single;
```

#### After
```php
// Initialize from settings before use
$cus_single = isset($referal_settings['referal_customer']) ? $referal_settings['referal_customer'] : '';
$emp_single = isset($referal_settings['referal_employee']) ? $referal_settings['referal_employee'] : '';
$data['referal_code_cus'] = $cus_single;
$data['referal_code_emp'] = $emp_single;
```

Also guard `is_opening` and session data:
```php
// Before
$data['is_opening'] = $account['is_opening'];

// After
$data['is_opening'] = isset($account['is_opening']) ? $account['is_opening'] : 0;
```

### Fix 3: Initialize variables in payment model (payment_model.php)

In `get_paymentContent()`, after `$record = $records->row();` add:

#### Before
```php
$record = $records->row();
$max_chance = ($record->max_chance == 0 ? 1 : $record->max_chance);
```

#### After
```php
$record = $records->row();
$allow_advancePay = 'N';
$allow_pendingPay = 'N';
$allow_cash_limit = 0;
$eligible_wgt = 0;
$dg_benefit_value = '';
$dg_benefit_type = '';
$dg_benefit_symbol = '';
$dg_benefit_content = '';
$max_chance = ($record->max_chance == 0 ? 1 : $record->max_chance);
```

Also guard `current_chances_use` in the return array:
```php
// Before
'current_chances_use' => $record->current_chances_use,

// After
'current_chances_use' => (isset($record->current_chances_use) ? $record->current_chances_use : 0),
```

### Fix 4: Initialize $mobile in SMS model (admin_usersms_model.php)

#### Before
```php
foreach ($query->result() as $row) {
    $mobile = $row->mobile;
}
return array('mobile' => $mobile, ...);
```

#### After
```php
$mobile = "";
foreach ($query->result() as $row) {
    $mobile = $row->mobile;
}
return array('mobile' => $mobile, ...);
```

## Verification
1. **Rapid typing test**: Type a 6-char referral code quickly. Open browser Network tab — should see only **1 request** fire (after 500ms pause), not 6.
2. **Abort test**: Type 3 chars, pause, type 3 more. First request should show as `(canceled)` in Network tab.
3. **Overlay test**: Abort should NOT leave the loading overlay stuck. The error handler skips `overlay.hide()` for abort status.
4. **Full enrollment flow**: Complete an account enrollment end-to-end — should redirect to payment page without 502.
5. **Payment page load**: The payment/add page should render without PHP notices in error log.
6. **Repeat 10 times**: Enroll 10 accounts in quick succession to confirm race condition is eliminated.

## Notes
- **This is a sibling of PAT-CSRF-001** — both are caused by CI2 cookie session races during concurrent AJAX. PAT-CSRF-001 manifests as "Invalid Form Submit" on payment; this one manifests as 502 on enrollment.
- **Permanent fix**: Migrate to `sess_use_database = TRUE` with a `ci_sessions` database table. This eliminates ALL cookie session race conditions across the entire application.
- **The 500ms debounce value** is conservative enough to avoid unnecessary requests but responsive enough that users won't notice delay. Can be adjusted to 300ms if needed.
- **Why abort() matters**: Even with debounce, without abort, a slow-responding server could result in two requests in flight simultaneously (debounce only prevents rapid-fire, not overlapping responses).
- **`display_errors` setting**: Even with `display_errors = Off` (which is correct for production), the variable initializations are important because NULL/missing values cause logic errors that can crash PHP-FPM workers with fatal errors.
