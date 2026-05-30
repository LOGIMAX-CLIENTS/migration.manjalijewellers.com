# Recipe: OTP Strict Comparison (== → ===)

## Metadata
- **Pattern ID**: PAT-SEC-002
- **Severity**: CRITICAL
- **Modules Affected**: Billing, Payment, Settings, Branch Transfer, Catalog, Manage, Reports, App API, Chit Admin
- **Auto-fixable**: Yes (string replacement)

## Symptom
OTP verification can be bypassed via PHP type juggling. An attacker sending `otp=0` or `otp=true` can pass `==` comparison against any stored OTP string, because PHP's `==` treats `0 == "any_string"` as `true`.

## Root Cause
PHP's loose comparison `==` coerces types. `"123456" == 0` is `true`. `"123456" == true` is `true`. Only strict `===` prevents this.

## Detection
```command
grep -rn "otp.*==\s" admin/application/controllers/ --include="*.php" -i
```

**Instance count in source**: 40+ across 8 controllers.

## Files
- `admin/application/controllers/admin_ret_billing.php` (7 instances)
- `admin/application/controllers/admin_manage.php` (5 instances)
- `admin/application/controllers/admin_payment.php` (1 instance)
- `admin/application/controllers/admin_ret_brntransfer.php` (2 instances)
- `admin/application/controllers/admin_ret_catalog.php` (1 instance)
- `admin/application/controllers/admin_reports.php` (2 instances)
- `admin/application/controllers/chit_admin.php` (2 instances)
- `admin/application/controllers/admin_app_api.php` (1 instance)

## Fix

> **Auto-fixable for direct OTP comparisons only.** Settings-check lines like `$otp['enable_otp'] == 1` should NOT be changed.

### Pattern 1: Direct OTP verification

#### Before
```php
if ($OTP == $post_otp) {
```

#### After
```php
if ($OTP === $post_otp) {
```

### Pattern 2: Session OTP check

#### Before
```php
if ($this->session->userdata('OTP') == $this->input->post('otp')) {
```

#### After
```php
if ($this->session->userdata('OTP') === $this->input->post('otp')) {
```

### Pattern 3: Variable OTP check

#### Before
```php
if ($otp == $user_otp) {
```

#### After
```php
if ($otp === $user_otp) {
```

### Pattern 4: Login OTP

#### Before
```php
if($input_otp == $this->session->userdata('login_OTP'))
```

#### After
```php
if($input_otp === $this->session->userdata('login_OTP'))
```

### Pattern 5: API OTP

#### Before
```php
if ($postdata['input_otp'] ==  $postdata['sys_otp']) {
```

#### After
```php
if ($postdata['input_otp'] ===  $postdata['sys_otp']) {
```

### EXCLUDE — Do NOT change these (settings checks, not OTP verification):
```php
// These compare integers/booleans, NOT OTP values:
if ($otp['enable_otp'] == 1)           // OK — settings flag
if ($emp_id['req_otplogin'] == 1)      // OK — settings flag
if ($entry_date[0]['req_gift_issue_otp'] == 1)  // OK — settings flag
```

## Verification
1. Search for remaining `otp.*==\s` — should only be settings checks
2. Test OTP flow: enter wrong OTP → should fail
3. Test bypass: send `otp=0` → should fail (was succeeding before)

## Notes
- The `otp_data['otp_code'] == $otp_entered` in admin_manage.php (line 1134) is the most critical — it validates OTP from DB
- Count instances per client to estimate fix scope
- Some clients may have already patched this in their customization — Before pattern won't match
