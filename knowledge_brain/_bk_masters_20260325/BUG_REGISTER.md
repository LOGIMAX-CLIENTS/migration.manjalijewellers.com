# Masters Module — Bug Register

> **Brain Updated:** 2026-03-18 | **Total Bugs:** 44 (R1: 20, R2: 11, R3: 7, R4: 6)

---

## Quick-Fix Reference

| Priority | Count | Notes |
|---|---|---|
| P0 Catastrophic | 1 | Fix before production — data destruction risk |
| P1 Critical | 9 | Fix immediately |
| P2 Medium | 21 | Security / data integrity / data loss |
| P3 Low | 13 | Code quality / performance |

---

## P0 — Catastrophic

### MST-BUG-001 | P0 | `clear_database()` — No Auth Gate, Any Employee Can Truncate ALL Data

**File:** `admin/application/controllers/admin_settings.php`  
**Lines:** L2147–2218  
**When:** Any logged-in user hits `/settings/clear_database`

**Root Cause:**
```php
function clear_database() {
    // No role check, no confirmation, no rate limit
    $truncate = array(
        'scheme_account', 'customer_reg', 'customer', 'address', 'kyc',
        'payment', 'receipt', 'transaction', 'wallet_account', ...
    );
    foreach ($truncate as $selected) {
        $this->truncateFromArray($selected);  // TRUNCATE TABLE
    }
}
```

`truncateFromArray()` (L2210):
```php
function truncateFromArray($tables) {
    foreach ($tables as $table) {
        $sql = "Truncate " . $table;  // ← runs immediately, no confirm
        $this->db->query($sql);
    }
}
```

**Impact:** Immediate, irreversible loss of all customer, payment, scheme, and transaction data. This was likely intended as a "demo reset" tool but has NO protection in production routes.

**Fix (multiple layers required):**
1. Add superadmin role check: `if ($this->session->userdata('profile') != 1) { redirect(); }`
2. Require POST with CSRF token + unique confirmation phrase
3. Remove from production routes entirely — should be a CLI-only script
4. Add rate limiting and IP whitelist

---

## P1 — Critical Bugs

### MST-BUG-002 | P1 | `get_access()` — SQL Injection in the RBAC Engine

**File:** `admin/application/models/admin_settings_model.php`  
**Line:** L103  
**When:** Every module permission check calls this

**Root Cause:**
```php
function get_access($url) {
    $sql = "Select ...
            where a.id_profile=" . $this->session->userdata('profile') .
            " and m.link='" . $url . "'";  // ← $url concatenated raw
    return $this->db->query($sql)->row_array();
}
```

Currently callers pass hardcoded strings like `get_access('settings/rate/list')` — safe. But if any future caller passes user-controlled input, this is SQL injection directly into the access control decision. The risk surface is the **most sensitive method in the system**.

**Fix:** `$url_safe = $this->db->escape_str($url);` and use `m.link='$url_safe'`

---

### MST-BUG-003 | P1 | `metal_rates('Save')` — `file_put_contents` Writes PHP "Array" Instead of JSON

**File:** `admin/application/controllers/admin_settings.php`  
**Line:** L570  
**When:** Admin saves any metal rate update

**Root Cause:**
```php
$rate_array = array(
    'goldrate_22ct' => ...,
    'silverrate_1gm' => ...,
    ...
);
file_put_contents('../api/rate.txt', $rate_array);
// ← file_put_contents with array → writes "Array" literally
// Mobile API reads this file; it receives "Array" not JSON
```

The correct call at L511 uses `json_encode($insertRate)` — but this second `file_put_contents` at L570 does not.  
**Impact:** After every rate save, mobile API rate file is corrupted to the string "Array". The mobile app's rate display shows nothing or crashes.

**Fix:** `file_put_contents('../api/rate.txt', json_encode($rate_array));`

---

### MST-BUG-004 | P1 | `get_gift_name_byId()` — Raw `$_POST['id']`

**File:** `admin/application/controllers/admin_settings.php`  
**Line:** L4029  
**Root Cause:**
```php
function get_gift_name_byId() {
    $id = $_POST['id'];  // ← raw POST
    // $id flows into model SQL query
```
**Fix:** `$id = $this->input->post('id');`

---

### MST-BUG-005 | P1 | `ajax_get_version()` — Raw `$_POST` Date Fields

**File:** `admin/application/controllers/admin_settings.php`  
**Lines:** L4264–4265  
```php
$from_date = $_POST['from_date'];  // ← raw POST
$to_date   = $_POST['to_date'];    // ← raw POST
```
These flow into `versionDB()` which uses them in a SQL query with BETWEEN.  
**Fix:** Use `$this->input->post('from_date')` + validate as date.

---

### MST-BUG-006 | P1 | Country/State/City/Pincode Handlers — Raw `$_POST`

**File:** `admin/application/controllers/admin_settings.php`  
**Lines:** L1054–1071  
```php
if (isset($_POST['id_country'])) {
    $data = $this->admin_settings_model->get_state($_POST['id_country']);
}
if (isset($_POST['id_state'])) {
    $data = $this->admin_settings_model->get_city($_POST['id_state']);
}
if (isset($_POST['pincode'])) {
    $data = $this->admin_settings_model->get_village_by_pincode($_POST['pincode']);
}
```

These feed into model queries as `WHERE id_country=$id_country`, etc — SQL injection.  
**Fix:** Use CI input helper + cast to int: `(int)$this->input->post('id_country')`

---

## P2 — Medium Priority

### MST-BUG-007 | P2 | `get_branch_rate()` — Raw `$id_branch` in SQL

**File:** `admin/application/models/admin_settings_model.php`  
**Lines:** L22–25  
```php
$sql = "select ... where b.id_branch=" . $id_branch;
```
**Fix:** Cast: `(int)$id_branch`

---

### MST-BUG-008 | P2 | `max_metalrate()` — Raw `$id_branch` in SQL

**File:** `admin/application/models/admin_settings_model.php`  
**Line:** L1022  
```php
$sql = "select max(m.id_metalrates) as max_id from metal_rates m" . 
       ($is_branchwise_rate == 1 && $id_branch != '' ? " left join ... where ... id_branch=" . $id_branch : "");
```
**Fix:** Cast to int.

---

### MST-BUG-009 | P2 | `settingsDB()` — Raw `$settings` in SQL

**File:** `admin/application/models/admin_settings_model.php`  
**Line:** L1158  
```php
$sql = "Select * From chit_settings Where settings='" . $settings . "'";
```
**Fix:** `$this->db->escape($settings)`

---

### MST-BUG-010 | P2 | `getBranchId()` — Raw `$warehouse` in SQL

**File:** `admin/application/models/admin_settings_model.php`  
**Line:** L2167  
```php
$sql = "SELECT id_branch FROM branch where expo_warehouse='" . $warehouse . "' or warehouse='" . $warehouse . "'";
```
**Fix:** `$this->db->escape($warehouse)`

---

### MST-BUG-011 | P2 | `get_version_data()` — Raw `$version_no` in SQL

**File:** `admin/application/models/admin_settings_model.php`  
**Line:** L2697  
```php
$sql = "Select * From version Where version_no='" . $version_no . "'";
```
**Fix:** `$this->db->escape_str($version_no)`

---

### MST-BUG-012 | P2 | `menu_generation()` — Raw `$id_profile` in SQL

**File:** `admin/application/models/admin_settings_model.php`  
**Line:** L58  
```php
" Where m.active =1 And m.id_menu>1 And a.view=1 And p.id_profile=" . $id_profile
```
Though `$id_profile` comes from session, it should still be cast: `(int)$id_profile`

---

### MST-BUG-013 | P2 | Duplicate Model Constant — `SET_MODEL` = `SETT_MOD`

**File:** `admin/application/controllers/admin_settings.php`  
**Lines:** L7 and L17  
```php
const SET_MODEL = "admin_settings_model";
const SETT_MOD  = "admin_settings_model";  // ← exact duplicate
```
And in constructor (L31 and L37), the model is loaded twice under the same class instance key — wasted initialization.  
**Fix:** Remove `SETT_MOD` constant, replace all `self::SETT_MOD` usages with `self::SET_MODEL`.

---

### MST-BUG-014 | P2 | 7× `mkdir(0777)` — World-Writable Image Directories

**File:** `admin/application/controllers/admin_settings.php`  
**Lines:** L2256, L2623, L2646, L2662, L3391, L3499, L3524  
All image directories (offers, new_arrivals, classification, payment_gateway, branch logo) created with 0777.  
**Fix:** `mkdir($path, 0755, TRUE)` everywhere.

---

### MST-BUG-015 | P2 | DB Backup Stored in Webroot

**File:** `admin/application/controllers/admin_settings.php`  
**Lines:** L2232–2270 (`db_backup()`)  
The backup is written to a `backups/` folder inside the webroot. Anyone who guesses (or enumerates) the filename can download the full database backup.  
**Fix:** Store backups outside webroot or protect with `.htaccess` deny-all.

---

### MST-BUG-016 | P2 | `PermissionDB()` — Hardcoded Menu IDs (17, 18)

**File:** `admin/application/models/admin_settings_model.php`  
**Line:** L200  
```php
($id != 1 ? " And m.id_menu<>17 " : "")  // id 17 = superadmin-only menu?
($id == 1 || $id == 2 ? "" : "  And m.id_menu<>18 ")
```
Menu IDs 17 and 18 are hardcoded — brittle if menu table changes. No comment explains what these menus are.  
**Fix:** Use `menu.link` string or `menu.label` as the exclusion criterion, or document why these IDs are special.

---

### MST-BUG-017 | P2 | `get_access()` Returns NULL Silently — Callers Don't Handle

**File:** Model L96–105  
**Pattern across ALL modules:**  
When `get_access()` finds no matching menu row, it returns an empty array. Callers do `$access['view']` without null-checking → PHP notice on every call for menus not configured for a profile.

---

## P3 — Low Priority

### MST-BUG-018 | P3 | 61 of 87 AJAX Calls Without Error Handlers

**File:** `admin/assets/js/admin_settings.js`  
87 AJAX `url:` blocks, 26 have `error:` handlers → **61 missing**.  
Critical ones missing: rate save, branch save, gateway config, SMS API config, village form.

---

### MST-BUG-019 | P3 | 72 `console.log` Statements in Production JS

**File:** `admin/assets/js/admin_settings.js`  
72 debug log calls left in production.

---

### MST-BUG-020 | P3 | Dead Code — Competitor URLs in Commented Block

**File:** `admin/application/controllers/admin_settings.php`  
**Lines:** L655–703  
Large commented-out `mjdma_update` case contains hardcoded URLs to competitor/client sites including credentials patterns. Should be removed from source entirely.

---

## R2 Bugs — Found in Round 2 (2026-03-17)

---

### MST-BUG-021 | P3 | `general_settings('View')` — Duplicate `promotion_crt_settings()` Call

**File:** Controller L1731–1732  
```php
$data['promotion_crt'] = $this->$model->promotion_crt_settings('get', 1);
$data['promotion']     = $this->$model->promotion_crt_settings('get', 1);  // ← exact duplicate
```
Same query fired twice, second result assigned to different key. Wasted DB call on every settings view load.  
**Fix:** Remove one call; reference `$data['promotion'] = $data['promotion_crt']`.

---

### MST-BUG-022 | P2 | `general_settings('Save'/'Update')` — App Version/Config Data Never Saved

**File:** Controller L1949, L2094  
```php
// SAVE case:
// $status_config = $this->$model->configDB('insert', $id, $config_dat);  ← commented out
// UPDATE case:
// $status_config = $this->$model->configDB('update', $id, $config_dat);  ← commented out
```
The `$config_dat` array (Play Store URL, Android/iOS app package names, current/new version numbers for force-update) is built but never persisted — both save and update paths have the configDB call commented out.  
**Impact:** Admins cannot set app version numbers via the settings panel — changes appear to save but are discarded.  
**Fix:** Uncomment both lines; add transaction check.

---

### MST-BUG-023 | P3 | `general_settings('Save'/'Update')` — `$general[tab_name]` Bare Constant in Log

**File:** Controller L1957, L2108  
```php
'record' => $general[tab_name],  // ← bare constant, not string key
```
PHP resolves bare word `tab_name` as an undefined constant → falls back to the string `"tab_name"`, emitting `E_NOTICE`. The log record field is always `"tab_name"` instead of the actual tab value.  
**Fix:** `$general['tab_name']`

---

### MST-BUG-024 | P3 | `general_settings('Update')` — `vs_booking_time` Half-Sided Check

**File:** Controller L2048  
```php
'vs_booking_time' => (isset($general['fn_from']) ? (isset($general['an_to']) ? $general['fn_from'] . '-' . $general['an_to'] : '') : 0),
```
If `fn_from` IS set but `an_to` is NOT: stores `''` (empty string) instead of `0`. All other boolean fields default to `0`. Type inconsistency in `chit_settings`.  
**Fix:** `... : '') : 0)` → both branches should default to `0`.

---

### MST-BUG-025 | P2 | `gateway_settings('Update_demo'/'Update_pro')` — Audit Log Written Before DB Result

**File:** Controller L2293–2300 (demo), L2319–2326 (pro)  
```php
$log_data = array('remark' => 'General Settings edited successfully');
// ← log recorded here unconditionally
$this->$log_model->log_detail('insert', '', $log_data);  // is NOT here but log_data built before check
if ($update['status']) { ... }
```
The log entry is built and recorded regardless of whether the gateway credential update succeeded. Audit trail shows success even on DB failure.  
**Fix:** Move log write inside `if ($update['status'])` block.

---

### MST-BUG-026 | P2 | `gateway_settings` — HDFC/Tech Gateways Missing Reciprocal `is_default` Toggle

**File:** Controller L2302, L2329  
```php
// Demo update: only toggles gateway id=2
$update = $this->$model->gateway_settingsDB('update', 2, array('is_default' => ...));
// Pro update: only toggles gateway id=1
$update = $this->$model->gateway_settingsDB('update', 1, array('is_default' => ...));
```
Gateways 3-6 (HDFC demo/pro, Tech demo/pro) have no reciprocal toggle — setting `is_default=1` on HDFC demo doesn't set HDFC pro to 0. Both can have `is_default=1` simultaneously, breaking gateway selection.  
**Fix:** Toggle the sibling gateway (id+1 or id-1) when updating each pair.

---

### MST-BUG-027 | P3 | `branch_form('Add')` — Array Compared to Integer

**File:** Controller L3038  
```php
$id_branch = $this->$model->insert_branch($branch_data);  // returns ['id_branch' => X]
if ($id_branch > 0) {  // ← array > int is always TRUE in PHP
    $result = $this->set__branch_image($id_branch['id_branch']);
}
```
Accidentally works because `['id_branch'=>X] > 0` is always true — but masks the intent.  
**Fix:** `if ($id_branch['id_branch'] > 0)`

---

### MST-BUG-028 | P1 | `branch_form('Update')` — `trans_status()` Without `trans_begin()`

**File:** Controller L3084–3103  
```php
case "Update":
    // ← NO trans_begin() here
    $update_id = $this->$model->update_branch($data, $id);
    if (isset($_FILES['file']['name'])) { ... }  // image upload runs outside any transaction
    if ($this->db->trans_status() === TRUE) {    // ← always TRUE
        $this->db->trans_commit();               // ← meaningless
        echo TRUE;
    }
```
Branch update has no transaction protection. If image upload fails after the DB update, there's no rollback. More critically, `trans_status()` without `trans_begin()` always returns TRUE → always echoes success to JS even on failure.  
**Fix:** Add `$this->db->trans_begin();` before `update_branch()` call.

---

### MST-BUG-029 | P1 | `send_RatesToAllUsers()` — Non-Branchwise Path Notifies Only Last Customer

**File:** Controller L3633–3643  
```php
foreach ($data['data'] as $r) {
    $arraycontent = array(
        'message' => $r['message'],
        ...
    );  // ← $arraycontent OVERWRITTEN each iteration, never dispatched inside loop
}
// ← loop ends
$send = $this->onesignalNotificationToAll($arraycontent);  // sends ONLY last customer's message
```
**Impact:** In non-branch-wise installations (common single-branch clients), metal rate push notification is only sent to the LAST customer in the result set. All other customers receive no notification despite the admin seeing "sent successfully".  
**Fix:** Move `onesignalNotificationToAll($arraycontent)` call INSIDE the foreach loop.

---

### MST-BUG-030 | P2 | `send_RatesToAllUsers()` — N+1 SQL Query Inside Customer Loop

**File:** Controller L3557, L3595  
```php
foreach ($cusData as $cus) {
    // INSIDE LOOP:
    $resultset = $this->db->query("SELECT noti_name, noti_footer, noti_msg FROM notification WHERE id_notification=1");
    // ← Same query repeated N times
}
```
The notification template is fetched once per customer. For 1000 customers this fires 1000 identical SELECTs.  
**Fix:** Pull the `$resultset` query above the outer customer loop.

---

### MST-BUG-031 | P2 | `onesignalNotificationToAll()` — `$alertdetails['footer']` Without Null Check

**File:** Controller L3662  
```php
'subtitle' => array("en" => $alertdetails['footer']),  // ← 'footer' key not guaranteed
```
The non-branchwise call path (L3633–3642) builds `$arraycontent` without a `'footer'` key. When `onesignalNotificationToAll()` is called, `$alertdetails['footer']` is undefined → E_NOTICE on every rate notification in non-branchwise mode.  
**Fix:** `'subtitle' => array("en" => (isset($alertdetails['footer']) ? $alertdetails['footer'] : ''))`

---

## R3 Bugs — Found in Round 3 (2026-03-17)

> **Correction to MST-BUG-015:** DB backup path is `'../data/backup/'` — OUTSIDE the webroot. R1 assessment was incorrect. Downgraded to P3/note. Real concern (no role check) captured as MST-BUG-033.

---

### MST-BUG-032 | P3 | `ledger('Save')` — No Numeric Validation on `opening_balance`

**File:** Controller L4573
```php
$opening_balance = $this->input->post('opening_balance');
// No is_numeric() or form_validation before use
```
**Fix:** Add `'rules' => 'required|numeric'` to form_validation for `opening_balance`.

---

### MST-BUG-033 | P2 | `db_backup()` — No Role Check Before Full DB Download

**File:** Controller L2232
```php
function db_backup() {
    // No profile/role check
    $backup = &$this->dbutil->backup($prefs);   // full database dump
    force_download($filename . '.zip', $backup);  // delivered to browser
}
```
Any logged-in employee can download a complete database backup with all customer data and payment details.
**Fix:** `if ($this->session->userdata('profile') != 1) { show_error('Forbidden', 403); }`

---

### MST-BUG-034 | P2 | `download()` — No Role Check + Hardcoded Relative CSV Path

**File:** Controller L3831
```php
function download() {
    $file_name = 'unregistered_cus.csv';  // relative to webroot
    readfile($file_name);                  // no role check, no file_exists check
}
```
**Fix:** Add role check + absolute path + `file_exists()` guard.

---

### MST-BUG-035 | P3 | `compress()` — Dead `$data` Assignment

**File:** Controller L3846
```php
$this->zip->download('customer_list.zip');
$data = array('File download successfully');  // ← never used
```
**Fix:** Remove dead line. Also add role check to this download.

---

### MST-BUG-036 | P2 | All Master View `list.php` — Flash Message Echoed Unescaped

**File:** 40+ files in `admin/application/views/master/*/list.php`
```php
echo $message['class'];    // in CSS class attribute
echo $message['title'];    // in <h4>
echo $message['message'];  // in div body
```
Today hardcoded — low immediate risk. Latent XSS if any controller ever interpolates user input into flash messages.
**Fix (systemic):** `htmlspecialchars($message['...'], ENT_QUOTES, 'UTF-8')` on all three echoes.

---

### MST-BUG-037 | P3 | `admin_settings.js` — 61 AJAX Calls Without Error Handlers

**File:** `admin/assets/js/admin_settings.js`
61 of 87 AJAX blocks lack `error:` callback. Highest-risk: rate save, branch save, gateway update, SMS API update.
**Fix:** Global `$.ajaxSetup` error handler or per-call `error: function(){ alert('Server error'); }`.

---

### MST-BUG-038 | P3 | ~15 Older Entity CRUDs Lack Form Validation

**File:** Controller — entities pre-2024 (drawee, classification, weight, country, state, city, department, designation)
No `form_validation` — blank/whitespace-only names can be saved. Newer entities (bank, payment_mode — 2025) have this fixed.
**Fix:** Retrofit `required|callback_valid_str` across all older entity CRUDs.

---

## R4 Bugs — Found in Round 4 (2026-03-18)

---

### MST-BUG-039 | P2 | `get_SMS_data()` — `$arraycontent` Overwritten in Loop, Only Last Message Used

**File:** Controller L782–788  
```php
foreach ($data['data'] as $r) {
    $arraycontent = $r['message'];  // ← overwrites every iteration
}
$sms_data['message'] = $arraycontent;  // ← only last customer's message
```
Identical bug pattern to MST-BUG-029. Currently called from a commented-out block, so latent.  
**Fix:** Collect messages into array, or dispatch SMS inside the loop per customer.

---

### MST-BUG-040 | P1 | `send_bulk_sms()` — Hardcoded Credentials in Source

**File:** Controller L806  
```php
'Authorization: Basic ' . base64_encode("lmx@uzhavan:lmx@2018")
```
API credentials for `nammauzhavan.com` SMS vendor are hardcoded inline and base64-encoded in source. Anyone with codebase access has these credentials. base64 is encoding, not encryption.

**Fix:** Move to `config/config.php`: `$config['promo_sms_creds'] = getenv('PROMO_SMS_CREDS')` or similar.

---

### MST-BUG-041 | P3 | `send_bulk_sms()` — Hardcoded HTTP Vendor URL

**File:** Controller L803  
```php
curl_setopt($ch, CURLOPT_URL, "http://nammauzhavan.com/api/v1/smjtvm_sendsms");
```
HTTP (not HTTPS) URL, hardcoded — not configurable, not secure.  
**Fix:** Move URL to config; use `https://` endpoint if vendor supports it.

---

### MST-BUG-042 | P1 | `metal_rates('Update')` — Flat File `rate.txt` Never Updated on Rate Edit

**File:** Controller L632  
```php
// $this->update_rate_file($data['rates']);  // ← COMMENTED OUT
```
When admin edits an existing metal rate, the `../api/rate.txt` flat file that the mobile API reads is NEVER updated. Mobile app serves stale rates until a new rate is inserted (not merely updated).  
**Fix:** Uncomment L632 (or replace with `$this->update_rate_file($data['rates'])`).

**Note:** MST-BUG-003 (L570 `file_put_contents($rate_array)` without `json_encode`) is a SEPARATE bug on the Save path. This bug is on the Update path — both must be fixed.

---

### MST-BUG-043 | P3 | `profile('Save'/'Update')` — No Field Validation

**File:** Controller L135–257  
The profile entity saves 30+ fields (allow_acc_closing, metalrate_edit, metal_rate_datelimit etc.) via `$this->input->post('profile')` with CI-safe input helpers, but no form_validation is run. `profile_name` can be blank; numeric/boolean fields like `metal_rate_datelimit` are unvalidated.  
**Fix:** Add `form_validation` with `required` for `profile_name` and `numeric` for applicable numeric fields.

---

### MST-BUG-044 | P2 | `metal_rates('Save')` — `$branch_id` Undefined When Non-Branchwise

**File:** Controller L571–590  
```php
if ($branch_settings==1 && $is_branchwise_rate==1 && ...) {
    $branch_id = array(explode(',', $branch_list));  // ← only set inside IF
}
if ($status) {
    if ($sendNoti) {
        $this->send_RatesToAllUsers($branch_id[0]);  // ← $branch_id UNDEFINED if branch_settings!=1
    }
}
```
For single-branch installations (`branch_settings=0`) or non-branchwise rates, `$branch_id` is never declared → `E_NOTICE: Undefined variable`; `send_RatesToAllUsers(null)` called with null arg. In the non-branchwise path, this triggers MST-BUG-029 (last-customer-only notification) AND passes null to the branchArr parameter.

**Fix:** Initialize `$branch_id = array(array());` before the condition, so `$branch_id[0]` is always a valid empty array.
