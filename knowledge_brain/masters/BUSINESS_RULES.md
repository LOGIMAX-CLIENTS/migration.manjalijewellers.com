# Masters Module — Business Rules

> **Brain Updated:** 2026-03-25 | **Round:** R5-Upgrade

---

## RULE-MST-001: Metal Rate Discount Formula

**Rule:** Gold and silver rates can have a configurable discount applied before storage.

**Formula:**
```
IF enableGoldrateDisc == 1:
    goldrate_22ct  = mjdmagoldrate_22ct  - goldDiscAmt
IF enableGoldrateDisc_18k == 1:
    goldrate_18ct  = market_gold_18ct    - goldDiscAmt_18k
IF enableSilver_rateDisc == 1:
    silverrate_1gm = mjdmasilverrate_1gm - silverDiscAmt
```

**Implementation:** Controller `metal_rates('Save')` L520–560
**Config:** `chit_settings` fields: `enableGoldrateDisc`, `goldDiscAmt`, `enableGoldrateDisc_18k`, `goldDiscAmt_18k`, `enableSilver_rateDisc`, `silverDiscAmt`
**Downstream:** Payment module, customer scheme calculations, mobile app via `rate.txt`

---

## RULE-MST-002: RBAC Permission Model

**Rule:** Access control uses a 3-table model: `profile` → `access` → `menu`. Every module calls `get_access($url)` to check CRUD permissions.

**Formula:**
```
Access = SELECT view, add, edit, delete
         FROM access a
         JOIN menu m ON a.id_menu = m.id_menu
         JOIN profile p ON p.id_profile = a.id_profile
         WHERE a.id_profile = session.profile AND m.link = $url

IF access.view == 0: BLOCK page load
IF access.add == 0: HIDE add button
IF access.edit == 0: HIDE edit button
IF access.delete == 0: HIDE delete button
```

**Implementation:** Model `get_access()` L96–105
**⚠️ Risk:** `$url` concatenated raw into SQL (MST-BUG-002); `get_access()` returns NULL if menu not configured → callers don't null-check (MST-BUG-017)

---

## RULE-MST-003: Branch-wise Metal Rates

**Rule:** When `branch_settings == 1` AND `is_branchwise_rate == 1`, each branch can have separate metal rates linked via `branch_rate` table.

**Formula:**
```
IF branch_settings == 1 AND is_branchwise_rate == 1:
    FOR each branch_id in selected branches:
        INSERT branch_rate {id_metalrate, id_branch, status=1}
ELSE:
    Single rate applies to all branches
```

**Implementation:** Controller `metal_rates('Save')` L571–590
**⚠️ Bug:** `$branch_id` undefined when non-branchwise (MST-BUG-044)

---

## RULE-MST-004: Device License Limits

**Rule:** Mobile devices per app type are limited by settings fields.

**Formula:**
```
Collection App:  max = chit_settings.chitCollectionEmpCount
Estimation App:  max = ret_settings.estimation_app_devices_count
```

**Implementation:** Employee module `enable_device()` — but settings managed here
**Config:** Single-row `chit_settings` (id=1)

---

## RULE-MST-005: Rate Notification Dispatch

**Rule:** After metal rate save, push notifications are sent to all customers via OneSignal.

**Formula:**
```
IF canSendNoti(1) == TRUE:
    IF branch_settings == 1:
        FOR each branch:
            Get customers for branch → send OneSignal push
    ELSE:
        Get ALL customers → send OneSignal push (⚠️ BROKEN — MST-BUG-029)
```

**Implementation:** Controller `send_RatesToAllUsers()` L3583–3690
**⚠️ Bug:** Non-branchwise path only notifies LAST customer (MST-BUG-029)

---

## RULE-MST-006: Entity CRUD Pattern (78 Master Entities)

**Rule:** All 78 master entities follow the same URL convention with a `switch($type)` pattern.

**Pattern:**
```
GET  /settings/{entity}/List          → list view
GET  /settings/{entity}/View          → form (add)
GET  /settings/{entity}/View/{id}     → form (edit)
POST /settings/{entity}/Save          → insert
POST /settings/{entity}/Update/{id}   → update
GET  /settings/{entity}/Delete/{id}   → delete (⚠️ GET = CSRF)
     /settings/{entity}               → AJAX data (default case)
```

**Implementation:** Each entity method has a `switch($type)` block
**⚠️ Note:** ~15 older entity CRUDs lack form validation (MST-BUG-038)

---

## RULE-MST-007: General Settings (chit_settings) — Single Source of Truth

**Rule:** `chit_settings` is a single-row table (id=1) that controls all system behavior. Changes propagate immediately (no cache).

**Key Fields:**
| Field | Controls |
|---|---|
| `branch_settings` | Multi-branch mode (0=single, 1=multi) |
| `integrationType` | ERP integration mode |
| `sms_gateway` | Selected SMS gateway |
| `emp_wallet_account_type` | Employee wallet auto-creation |
| `schemeaccNo_displayFrmt` | Account number display format |
| `chitCollectionEmpCount` | Max collection app devices |

**Implementation:** `settingsDB('get/update')`, model L1162
**Validation:** Transaction-wrapped on Save/Update

---

## RULE-MST-008: Branch Auto-Setup on Create

**Rule:** When a new branch is created, these records are auto-created:

**Formula:**
```
INSERT branch → get id_branch
INSERT metal_rate_settings (default rate display config)
INSERT chit_settings CLONE for branch (copy from id=1)
INSERT employee_settings for admin (full-day access)
IF logo uploaded: mkdir + save image
```

**Implementation:** Controller `branch_form('Save')` L3027–3060
**⚠️ Bug:** `trans_status()` without `trans_begin()` on Update path (MST-BUG-028)

---

## RULE-MST-009: Payment Gateway Default Toggle

**Rule:** Only one payment gateway per type (demo/production) should be active (`is_default = 1`).

**Formula:**
```
Gateway pairs:
  Cashfree: id=1 (pro), id=2 (demo)
  HDFC:     id=3 (pro), id=4 (demo)
  Tech:     id=5 (pro), id=6 (demo)

Setting is_default=1 on one should set is_default=0 on sibling
```

**Implementation:** Controller `gateway_settings()` L2320+
**⚠️ Bug:** HDFC/Tech pairs don't toggle siblings (MST-BUG-026)

---

## RULE-MST-010: Image Upload Pattern

**Rule:** All entity images (offers, new_arrivals, classification, gateway, branch logo) follow the same upload pattern.

**Formula:**
```
1. mkdir(assets/img/{entity}/{id}/, 0777)  ← world-writable
2. GD process: resize/crop to target dimensions
3. Save as {entity}.jpg
4. UPDATE {entity} SET image_path = '{entity}/{id}/{entity}.jpg'
```

**Implementation:** `set_image()`, `set__branch_image()`, `set__clsfy_image()`, `set__paymentgateway_image()`
**⚠️ All use 0777 permissions (MST-BUG-014)**

---

## RULE-MST-011: Database Clear (Demo Reset)

**Rule:** `clear_database()` truncates all transactional tables for demo/test reset.

**Tables truncated:** scheme_account, customer_reg, customer, address, kyc, payment, receipt, transaction, wallet_account, and more

**Implementation:** Controller `clear_database()` L2190–2218
**⚠️ P0 CATASTROPHIC:** No auth check, no confirmation, no CSRF protection (MST-BUG-001)

---

## RULE-MST-012: Profile Permission Flags

**Rule:** Each profile has feature-level boolean flags beyond basic CRUD permissions.

**Key flags:**
| Flag | Purpose |
|---|---|
| `allow_acc_closing` | Can close accounts |
| `metalrate_edit` | Can edit metal rates |
| `metal_rate_datelimit` | Rate date limit |
| `req_otplogin` | OTP required for login |

**Implementation:** `profileDB()` model L160, Controller `profile()` L116
