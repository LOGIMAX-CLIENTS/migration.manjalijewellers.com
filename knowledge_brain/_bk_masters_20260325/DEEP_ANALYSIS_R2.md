# Masters Module — Deep Analysis Round 2

> **Brain Updated:** 2026-03-17 | **Round:** 2 Complete

---

## Scope Covered in R2

1. `general_settings()` complete trace (L1706–2121)
2. `gateway_settings()` + `gateway_form()` complete trace
3. `sms_api_settings()` + `mail_settings()` + `limit_settings()` + `discount_settings()` trace
4. `branch_form()` complete trace (L2984–3130)
5. `wallettype_account()` + `ref_benefits_setting()` trace
6. `send_RatesToAllUsers()` complete trace (L3540–3648)
7. `onesignalNotificationToAll()` trace (L3649–3682)

---

## 1. `general_settings()` — Complete Trace

### View form (L1720–1832)

The form load is one of the most expensive DB reads in the system — fires **12 model queries** on every `GET /settings/general/View/1`:

```
settingsDB('get', $id)    → chit_settings row
limitDB('get', $id)       → chit_limit row
discount_db('get', $id)   → chit_discount row
gateway_settingsDB('get_id', 1..6) × 6 calls → payment_gateway_settings rows 1-6
promotion_crt_settings('get', 1) ×2 (duplicate! bug)
otp_crt_settings('get', 1)
sms_apiDB('get', 1)
get_company() + get_curr_detail() + get_default_country/city/state ×3
customer_count() + scheme_count() + sch_acc_count() × 3 cross-model reads
configDB('get', $id)
customer_model::getFormatFromDB()
```

**Bug R2-001:** `$data['promotion_crt']` and `$data['promotion']` are both assigned from `promotion_crt_settings('get', 1)` — duplicate call, same data (L1731-1732).

### Save (L1834–1968) — NEW BUGS FOUND

**Bug R2-002 — `general_settings('Save')` — Config insert COMMENTED OUT:**
```php
// $status_config = $this->$model->configDB('insert', $id, $config_dat);  // ← commented out L1949
// ...
if($status) {  // ← only checks settingsDB insert, never configDB
```
The `$config_dat` array (app versions, Play Store URL, iOS pack) is built but **never saved** in the 'Save' (new record) flow. Only the Update case matters in production (always updating id=1), so this is low-risk but the dead code misleads.

**Bug R2-003 — `general_settings('Save'/'Update')` — `$general[tab_name]` Bare Constant:**
```php
// Line L1957 AND L2108:
'record' => $general[tab_name],
```
`tab_name` is used as a bare PHP constant (not a string key). PHP would try to resolve `tab_name` as a constant → falls back to the string `'tab_name'` with an `E_NOTICE`. Since CI suppresses notices in production, this silently logs the wrong value. Should be `$general['tab_name']`.

**Bug R2-004 — `general_settings('Update')` — `vs_booking_time` Concatenation Logic:**
```php
'vs_booking_time' => (isset($general['fn_from']) ? (isset($general['an_to']) ? $general['fn_from'] . '-' . $general['an_to'] : '') : 0),
```
Missing `$general['an_to']` existence check for the other side — if `fn_from` is set but `an_to` is missing, stores empty string instead of 0 (type inconsistency with other int fields defaulting to 0).

**Bug R2-005 — `general_settings('Update')` — Missing `config` save:**
```php
// $status_config = $this->$model->configDB('update', $id, $config_dat);  // ← commented out L2094
```
Same pattern — config data (app version numbers, Play Store URL) is built but **never persisted** in the Update flow either. This means **app version displayed via admin panel is never actually stored**.

---

## 2. `gateway_settings()` — Payment Gateway Credentials

### Architecture
Two gateway patterns exist:
1. **Legacy chit gateway** (L2277–2336): `gateway_settingsDB()` — manages records with `id_gateway` 1-6 (Cashfree demo/pro, HDFC demo/pro, Tech demo/pro)
2. **Retail gateway form** (L3406–3483): `gateway_form()` — manages `payment_gateway` table entries with image

### Security Analysis — `gateway_settings()`

```php
case 'Update_demo':
    $demo = $this->input->post('demo');
    $data = array(
        'key'  => $demo['key'],    // ← payment gateway API key from POST
        'salt' => $demo['salt'],   // ← payment gateway salt from POST
        ...
        'is_default' => $demo['is_default'],
    );
    $update = $this->$model->gateway_settingsDB('update', $id, $data);
    // IF update['status']:
    //   Also toggle the OTHER gateway's is_default (hardcoded id=1 or id=2)
```

**Bug R2-006 — `gateway_settings('Update_demo')` — Log written before checking update success:**
```php
$log_data = array(...);
// ← log written here BEFORE checking if update succeeded (L2293–2300)
if ($update['status']) {
    // ← log should be after this check
```
Gateway credential changes are logged regardless of whether the DB update succeeded.

**Bug R2-007 — `gateway_settings` — Hardcoded `is_default` toggle (id=1/2):**
```php
// When updating demo (id=whatever passed): toggle id=2's is_default
$update = $this->$model->gateway_settingsDB('update', 2, array('is_default' => ...));
// When updating pro: toggle id=1's is_default
$update = $this->$model->gateway_settingsDB('update', 1, array('is_default' => ...));
```
The demo/pro toggle for HDFC and Techprocess gateways (ids 3-6) does NOT have reciprocal toggles — they can both have `is_default=1` simultaneously. This means the gateway selection logic is broken for HDFC and Tech gateways if both are set as default.

---

## 3. `branch_form('Add')` — Transaction Issue

```php
case "Add":
    $this->db->trans_begin();
    $id_branch = $this->$model->insert_branch($branch_data); // returns ['id_branch' => X]
    if ($id_branch['id_branch']) {
        $this->$model->insertData($branchid, 'ret_day_closing');
    }
    if (isset($_FILES['file']['name'])) {
        if ($id_branch > 0) {  // ← BUG: comparing ARRAY to 0
            $result = $this->set__branch_image($id_branch['id_branch']);
        }
    }
    if ($this->db->trans_status() === TRUE) {
        $this->db->trans_commit();
        echo TRUE;
    }
```

**Bug R2-008 — `branch_form('Add')` — `$id_branch > 0` compares ARRAY:**
At L3038: `if ($id_branch > 0)` — `$id_branch` is `$this->$model->insert_branch()` return which is `['id_branch' => X]`. Comparing array > 0 in PHP returns TRUE (array is greater than int) — so this check always passes, but the correct check should be `if ($id_branch['id_branch'] > 0)`. Accidentally works correctly today, but masks the intent and would break if the return format changes.

**Bug R2-009 — `branch_form('Update')` — Missing `trans_begin()`:**
```php
case "Update":
    // NO trans_begin() call
    $update_id = $this->$model->update_branch($data, $id);
    // ...
    if ($this->db->trans_status() === TRUE) {  // ← ALWAYS TRUE (no trans_begin)
        $this->db->trans_commit();
        echo TRUE;
```
Branch update uses `trans_status()` without `trans_begin()` — same pattern as EMP-BUG-002. The commit is meaningless.

---

## 4. `send_RatesToAllUsers()` — Rate Notification Deep Trace

```
send_RatesToAllUsers($branchArr):
├─ get_cusnotiData('1') OR check_noti_settings()  
├─ IF is_branchwise_rate == 1 AND sizeof($branchArr) > 0:
│    IF is_branchwise_cus_reg == 1:
│        FOR each branch:
│            get_cusBranchRate($branch) → SELECT customer tokens by branch
│            FOR each customer:
│                SELECT noti_name, noti_footer, noti_msg FROM notification WHERE id_notification=1
│                ← This SELECT IS INSIDE THE INNER LOOP — N+1 QUERY
│                Build message, send push via send_singlealert_rate_notification()
│    ELSE:
│        get_account(implode(",", $branchArr))
│        FOR each account:
│            get_metal_rateby_branch($acc['id_customer'])
│            SELECT noti_... FROM notification WHERE id_notification=1
│            ← Also inside loop — N+1 QUERY
│            FOR each rate: build message
│            send_singlealert_rate_notification()
└─ ELSE (non-branchwise):
     get_cusnotiData('1') → already called at top
     FOR each customer in data:
         build arraycontent  ← Loop body only sets $arraycontent, never dispatches
     ← BUG: send is AFTER the loop → only last customer's notification sent
     $send = $this->onesignalNotificationToAll($arraycontent)
```

**Bug R2-010 — `send_RatesToAllUsers()` — Non-branchwise push loop: only last notification sent:**
```php
foreach ($data['data'] as $r) {
    $arraycontent = array(...);  // ← overwrites each iteration
}
// ← loop ends, $arraycontent = last customer's data ONLY
$send = $this->onesignalNotificationToAll($arraycontent);
```
For non-branch-wise rate notifications, only the last customer receives the notification. All others are silently skipped.

**Bug R2-011 — `send_RatesToAllUsers()` — N+1 query inside inner loop:**
`SELECT ... FROM notification WHERE id_notification=1` is executed once per customer inside the nested loop. This query should be pulled out above the loop. For 1000 customers, this fires 1000+ identical SELECT queries.

**Bug R2-012 — `onesignalNotificationToAll()` — `$alertdetails['footer']` accessed without null check:**
```php
'subtitle' => array("en" => $alertdetails['footer']),  // ← L3662
```
From `send_RatesToAllUsers()` non-branchwise path (L3633–3642), `$arraycontent` does not include a `'footer'` key. This throws `E_NOTICE: undefined index 'footer'`.

---

## 5. `wallettype_account()` + `ref_benefits_setting()` — Trace

Both follow the safe pattern:
```php
function wallettype_account($id) {
    $general = $this->input->post('general');
    $data = array(
        'emp_wallet_account_type' => ...,
        'wallet_points' => ...
    );
    $update = $this->$model->settingsDB('update', $id, $data);
    // log + redirect
}

function ref_benefits_setting($id) {
    $general = $this->input->post('general');
    $data = array(
        'cusplan_type' => ...,
        'allow_referral' => ...,
        'emp_ref_by' => ...
        // reference/benefit settings
    );
    $update = $this->$model->settingsDB('update', $id, $data);
}
```

Both use `$this->input->post()` (CI-sanitized) and update `chit_settings` via `settingsDB()` which uses CI query builder with WHERE clause — **no injection risk**. These are clean.

---

## 6. New Bugs Found in R2

| Bug ID | P | Location | Description |
|---|---|---|---|
| MST-BUG-021 | P3 | Controller L1731–1732 | `general_settings('View')`: `promotion_crt_settings()` called twice → wasted DB query |
| MST-BUG-022 | P2 | Controller L1949, L2094 | `general_settings`: `configDB` insert/update commented out → app version numbers never saved |
| MST-BUG-023 | P3 | Controller L1957, L2108 | `$general[tab_name]` — bare constant in log → `E_NOTICE` + wrong log value |
| MST-BUG-024 | P3 | Controller L2048 | `vs_booking_time` field: half-sided isset check → stores empty string instead of 0 |
| MST-BUG-025 | P2 | Controller L2293 | `gateway_settings`: audit log written before checking if DB update succeeded |
| MST-BUG-026 | P2 | Controller L2302/2329 | `gateway_settings`: HDFC/Tech gateway reciprocal `is_default` toggle missing → can have 2 active gateways |
| MST-BUG-027 | P3 | Controller L3038 | `branch_form('Add')`: `$id_branch > 0` compares array (accidentally works but misleading) |
| MST-BUG-028 | P1 | Controller L3097 | `branch_form('Update')`: `trans_status()` without `trans_begin()` — no transaction protection |
| MST-BUG-029 | P1 | Controller L3633–3643 | `send_RatesToAllUsers()`: non-branchwise path sends push to only LAST customer in list |
| MST-BUG-030 | P2 | Controller L3557, L3595 | `send_RatesToAllUsers()`: N+1 query — notification template SELECT inside customer loop |
| MST-BUG-031 | P2 | Controller L3662 | `onesignalNotificationToAll()`: `$alertdetails['footer']` accessed without null check → E_NOTICE |

---

## 7. Security Summary — R2 Additions

| Area | Status | Notes |
|---|---|---|
| `general_settings()` POST inputs | ✅ SAFE | All via `$this->input->post()` |
| `gateway_settings()` POST inputs | ✅ SAFE | All via `$this->input->post()` |
| `branch_form()` POST inputs | ✅ SAFE | All via `$this->input->post()` |
| `wallettype_account()` | ✅ SAFE | CI input, CI query builder |
| `ref_benefits_setting()` | ✅ SAFE | CI input, CI query builder |
| `send_RatesToAllUsers()` SQL | ✅ SAFE | Raw query but `id_notification=1` is hardcoded |
| `onesignalNotificationToAll()` | ⚠️ | SSL verify disabled + no error handling on curl |

The largest sections of the controller (general_settings, gateway, branch, wallet/referral) are **input-safe** — they all use CodeIgniter's input helper. The SQL injection risks identified in R1 are in model utility methods and AJAX lookup methods, not in the main form save flows.
