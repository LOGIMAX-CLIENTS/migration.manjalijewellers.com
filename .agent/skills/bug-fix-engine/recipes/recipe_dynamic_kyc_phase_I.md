# Recipe: Dynamic KYC System — Phase I

## Metadata
- **Pattern ID**: PAT-KYC-DYNAMIC-001
- **Severity**: HIGH
- **Modules Affected**: Customer Registration, Payment, Scheme Account Opening, Admin Settings, Reports, KYC Master CRUD
- **Auto-fixable**: No — requires DB migration + new files + multi-layer code changes
- **Source Commits**:
  - `c1a66c1` — Dynamic KYC update phase I (27 files, 3475 insertions)
  - `42f311f` — Digi gold closing issue fixed + KYC issues resolved
  - `5ae5d4c` — Minor changes for KYC (digit validation, webcam fixes)
  - `00cb3b6` — Allow pay checked with KYC (payment gating finalized)

## Client Scope
- **Applies to**: ALL chit/payment clients
- **Reason**: Clients using static KYC fields (PAN/Aadhar) hardcoded in the customer form. This replaces them with a fully dynamic, rule-driven KYC collection system configurable per-scheme and per-customer lifecycle event.

## Created By
- **Developer**: Antigravity
- **Client**: dineshjewellery.in
- **Date**: 2026-04-25

---

## Problem

KYC document collection was:
1. **Hardcoded** into the customer registration form (PAN, Voter ID, Ration Card, Aadhar as fixed fields).
2. **Not rule-driven** — no concept of "collect KYC only when payment exceeds ₹X" or "collect at scheme joining".
3. **Not configurable** — adding a new document type required code changes.
4. **Not gated** — no enforcement that KYC must be submitted before a payment or scheme account is created.
5. **No admin UI** to manage KYC document masters, attributes, rules, or settings.

---

## Solution Overview

Replace the static KYC system with a fully dynamic, database-driven architecture:

- **`kyc_master`** table — configurable document types (PAN, Aadhar, DL, Voter ID, etc.)
- **`kyc_attribute`** table — per-document input fields (text, image, validation regex)
- **`kyc_rules`** table — when to trigger KYC (on customer creation, scheme joining, payment threshold, installment count)
- **`kyc_settings`** table — global KYC switches (required on/off, mode, verification type, gating behaviour, logic)
- **`kyc`** table (existing) — stores submitted customer KYC data

---

## Database Migration

Run the following SQL in order:

```sql
-- ─────────────────────────────────────────────
-- 1. KYC Master — document type registry
-- ─────────────────────────────────────────────
CREATE TABLE `kyc_master` (
  `id_mas_kyc`       int(11)       NOT NULL AUTO_INCREMENT,
  `name`             varchar(20)   COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Document Name',
  `short_code`       varchar(30)   COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_type`         tinyint(1)    DEFAULT NULL COMMENT '1=Identity, 2=Address, 3=ID+Address, 4=Transaction Proof',
  `is_attachment_req` tinyint(1)   DEFAULT 0,
  `is_mandatory`     int(2)        NOT NULL DEFAULT 0 COMMENT '0=No, 1=Yes',
  `created_by`       int(11)       DEFAULT NULL,
  `created_on`       datetime      DEFAULT NULL,
  `updated_by`       int(11)       DEFAULT NULL,
  `updated_on`       datetime      DEFAULT NULL,
  `status`           tinyint(4)    NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `sort`             int(5)        DEFAULT NULL,
  PRIMARY KEY (`id_mas_kyc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kyc_master` VALUES
(1, 'PAN',             'PAN',    4, 0, 0, 1, NOW(), NULL, NULL, 1, 4),
(2, 'AADHAR',          'ADR_ID', 2, 0, 0, 1, NOW(), NULL, NULL, 1, 1),
(3, 'DRIVING LICENCE', 'DL',     2, 0, 0, 1, NOW(), NULL, NULL, 1, 2),
(4, 'VOTER ID',        'V_ID',   2, 0, 0, 1, NOW(), NULL, NULL, 1, 3);

-- ─────────────────────────────────────────────
-- 2. KYC Attribute — per-document input fields
-- ─────────────────────────────────────────────
CREATE TABLE `kyc_attribute` (
  `id_kyc_attribute` int(10)       NOT NULL AUTO_INCREMENT,
  `id_mas_kyc`       int(11)       NOT NULL,
  `attribute`        varchar(250)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `attr_label`       varchar(250)  COLLATE utf8mb4_unicode_ci NOT NULL,
  `attr_input`       varchar(45)   COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'varchar|number|image',
  `attr_length`      varchar(10)   COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_mandatory`     tinyint(1)    NOT NULL DEFAULT 0,
  `reg_expression`   text          COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position`         int(10)       DEFAULT NULL,
  `status`           tinyint(1)    NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_kyc_attribute`),
  UNIQUE KEY `id_mas_kyc_2` (`id_mas_kyc`, `position`),
  KEY `id_mas_kyc` (`id_mas_kyc`),
  CONSTRAINT `fk_id_mas_kyc` FOREIGN KEY (`id_mas_kyc`) REFERENCES `kyc_master` (`id_mas_kyc`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kyc_attribute` VALUES
(1,  1, 'pan_no',          'PAN NO',              'varchar', '10', 1, '^[a-zA-Z]{5}\\d{4}[a-zA-Z]{1}$', 1, 1),
(2,  1, 'pan_front_img',   'PAN FRONT IMAGE',     'image',   NULL, 1, NULL, 2, 1),
(3,  1, 'pan_back_img',    'PAN BACK IMAGE',      'image',   NULL, 0, NULL, 3, 1),
(4,  2, 'aadhar_no',       'AADHAR NO.',          'varchar', '14', 1, '^\\d{4}\\s\\d{4}\\s\\d{4}$', 1, 1),
(5,  2, 'aadhar_front_img','AADHAR FRONT IMAGE',  'image',   NULL, 1, NULL, 2, 1),
(6,  2, 'aadhar_back_img', 'AADHAR BACK IMAGE',   'image',   NULL, 1, NULL, 3, 1),
(7,  3, 'dl_no',           'DRIVING LICENCE NO.', 'varchar', '16', 1, '^(([A-Z]{2}[0-9]{2})( )|([A-Z]{2}-[0-9]{2}))((19|20)[0-9][0-9])[0-9]{7}$', 1, 1),
(8,  3, 'dl_front_img',    'DL FRONT IMAGE',      'image',   NULL, 1, NULL, 2, 1),
(9,  3, 'dl_back_img',     'DL BACK IMAGE',       'image',   NULL, 1, NULL, 3, 1),
(10, 4, 'vi_no',           'VOTER ID NO.',        'varchar', '10', 1, '^[A-Z]{3}[0-9]{7}$', 1, 1),
(11, 4, 'vi_front_img',    'VOTERID FRONT IMAGE', 'image',   NULL, 1, NULL, 2, 1),
(12, 4, 'vi_back_img',     'VOTERID BACK IMAGE',  'image',   NULL, 1, NULL, 3, 1);

-- ─────────────────────────────────────────────
-- 3. KYC Settings — global KYC configuration
-- ─────────────────────────────────────────────
CREATE TABLE `kyc_settings` (
  `id_kyc_settings`       int(11)    NOT NULL AUTO_INCREMENT,
  `kyc_required`          tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not required, 1=required',
  `kyc_mode`              tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=scheme_wise, 1=customer_wise',
  `kyc_integration_type`  tinyint(1) DEFAULT 0 COMMENT '0=Self, 1=Third-party',
  `kyc_verification_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=manual, 1=auto',
  `kyc_allow_type`        tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=allow payment, 1=block payment',
  `kyc_scheme_rule_logic`   tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=All Required, 1=At Least One',
  `kyc_customer_rule_logic` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=All Required, 1=At Least One',
  PRIMARY KEY (`id_kyc_settings`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `kyc_settings`
  (`kyc_required`, `kyc_mode`, `kyc_integration_type`, `kyc_verification_type`, `kyc_allow_type`)
VALUES (1, 0, 0, 0, 0);

-- ─────────────────────────────────────────────
-- 4. KYC Rules — when to trigger collection
-- ─────────────────────────────────────────────
CREATE TABLE `kyc_rules` (
  `id_kyc_rules` int(11)    NOT NULL AUTO_INCREMENT,
  `id_scheme`    varchar(50) DEFAULT NULL COMMENT 'NULL = all schemes',
  `id_mas_kyc`   int(11)    DEFAULT NULL COMMENT 'Which document to collect',
  `rules`        tinyint(4) DEFAULT NULL COMMENT 'scheme_wise: 0=on joining, 1=on payment, 2=on closing | customer_wise: 0=customer creation, 1=overall amount',
  `type`         tinyint(4) DEFAULT NULL COMMENT 'scheme_wise+on_payment: 0=amount, 1=installment',
  `amount`       int(11)    DEFAULT NULL COMMENT 'Threshold amount or installment count',
  `logic`        tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=All Required, 1=At Least One',
  `status`       tinyint(4) NOT NULL DEFAULT 1,
  `created_at`   datetime   NOT NULL DEFAULT current_timestamp(),
  `created_by`   smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id_kyc_rules`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ─────────────────────────────────────────────
-- 5. KYC table (existing) — ensure it exists
-- ─────────────────────────────────────────────
-- Existing `kyc` table columns used:
-- id_kyc, id_customer, kyc_type (→ id_mas_kyc), number, img_url,
-- back_img_url, document_url, status, verification_type,
-- added_by, emp_verified_by, date_add, last_update
-- status: 0=Pending, 1=Approved, 2=Auto-Verified, 3=Rejected
```

---

## New Files (create from scratch)

| File | Purpose |
|---|---|
| `admin/application/controllers/kyc.php` | KYC Master CRUD controller (list, add, edit, delete masters + attributes) |
| `admin/application/models/kyc_model.php` | All KYC logic: rule evaluation, unified data fetch, validation, save (customer + scheme account) |
| `admin/application/views/master/kyc/list.php` | KYC Master list page |
| `admin/application/views/master/kyc/form.php` | KYC Master add/edit form (with dynamic attribute rows) |
| `admin/application/views/master/customer/kyc_modal.php` | Shared KYC collection modal shell (header + AJAX content zone) |
| `admin/application/views/master/customer/kyc_tab.php` | AJAX-rendered KYC document tabs (dynamically loads per triggered rules) |
| `admin/assets/css/kyc_modal.css` | Unified premium CSS for all KYC modals (loaded via footer.php conditionally) |
| `admin/assets/js/kyc.js` | All KYC frontend logic: tab switching, webcam capture, file browse, base64 encode, form submit |

---

## Modified Files

### `admin/application/config/routes.php`
Add KYC controller routes:

```php
$route['kyc']            = 'kyc/index';
$route['kyc/add']        = 'kyc/form';
$route['kyc/edit/(:num)'] = 'kyc/form/$1';
$route['kyc/delete/(:num)'] = 'kyc/delete/$1';
$route['kyc/get_attributes/(:num)'] = 'kyc/get_attributes/$1';
```

---

### `admin/application/controllers/admin_settings.php`
Add KYC settings load, save, and rule management methods:

```php
// Load KYC settings into form
public function kyc_settings() { ... }

// Save KYC settings (upsert kyc_settings row)
public function save_kyc_settings() { ... }

// KYC rules CRUD (add_kyc_rule, delete_kyc_rule, list_kyc_rules)
public function add_kyc_rule() { ... }
public function delete_kyc_rule() { ... }
```

> **Bug note**: original `save_kyc_settings` used wrong column name `id_kyc_setting` (missing `s`). Correct PK is `id_kyc_settings`.

---

### `admin/application/models/admin_settings_model.php`
Add KYC settings read/write model methods:

```php
// Get all KYC settings
public function get_kyc_settings() {
    return $this->db->get('kyc_settings')->row_array();
}

// Upsert KYC settings
public function save_kyc_settings($data) {
    $existing = $this->db->get('kyc_settings')->row_array();
    if ($existing) {
        $this->db->where('id_kyc_settings', $existing['id_kyc_settings']);
        return $this->db->update('kyc_settings', $data);
    }
    return $this->db->insert('kyc_settings', $data);
}

// KYC rules CRUD
public function get_kyc_rules() { ... }
public function save_kyc_rule($data) { ... }
public function delete_kyc_rule($id) { ... }
```

---

### `admin/application/views/settings/general/form.php`
Add KYC settings panel (after existing settings sections):

```html
<!-- KYC Settings Panel -->
<div class="kyc-settings-section">
  <!-- kyc_required, kyc_mode, kyc_integration_type, kyc_verification_type, kyc_allow_type -->
  <!-- kyc_scheme_rule_logic, kyc_customer_rule_logic toggles -->
  <!-- KYC Rules table: add rule (scheme, document, trigger type, threshold, logic) -->
</div>
```

JS in `admin_settings.js` — handles KYC settings form submit + rule add/delete AJAX.

---

### `admin/application/controllers/admin_customer.php`
Hook `save_dynamic_kyc()` into both Create and Edit transaction flows:

```php
// Inside trans_begin() block, AFTER set_image():
$this->save_dynamic_kyc($cus_id);   // Create
$this->save_dynamic_kyc($id);        // Edit

// Private method at bottom of controller:
private function save_dynamic_kyc($cus_id) {
    $kyc_dynamic_post = $this->input->post('kyc_dynamic');
    if (!is_array($kyc_dynamic_post) || empty($kyc_dynamic_post)) {
        return;
    }
    $this->load->model('kyc_model');
    $this->kyc_model->save_customer_kyc($cus_id, $kyc_dynamic_post);
}
```

> **Important**: Do NOT put `file_put_contents` debug calls inside `_process_save_kyc` — large base64 payloads will exhaust PHP memory mid-transaction and cause silent rollbacks.

---

### `admin/application/models/customer_model.php`
Add method to check if customer has pending/missing KYC before payment gating.

---

### `application/models/kyc_model.php` ⚠️ MUST ALSO UPDATE (App-side)

The application layer (mobile/REST API) has its own `kyc_model.php` at `application/models/kyc_model.php`. This must be copied and adapted from the admin version.

Key difference: the app-side model does **not** use `$this->session` for `uid` — it reads from the customer session or API token context.

Ensure both files contain:
- `get_unified_dynamic_kyc_data($id_customer, $id_scheme)` — rule evaluation
- `save_customer_kyc($cus_id, $kyc_data_array)` — atomic save with transaction
- `_process_save_kyc($cus_id, $id_mas_kyc, $data)` — file decode + disk save

> **Critical**: The app-side `kyc_model.php` must have the same paid_installments logic fix applied if it includes any scheme account queries. After the `paid_installments_sql()` helper migration, add `$paidInstallmentsSQL = paid_installments_sql('p', 'sa', 's');` and update any inline SQL using the hardcoded pattern.

---

### `admin/application/models/payment_model.php`
Add KYC gating check in `get_paymentContent` and payment save flows:

```php
// Load KYC data alongside scheme account details
$kyc_data = $this->load->model('kyc_model');
// Pass kyc_required flag + existing kyc status into payment form AJAX response
```

**`id_scheme` pass-through fix** (commit `42f311f`): When calling `get_paymentContent`, the `id_scheme` was not included in the form POST, causing KYC rule evaluation to receive `id_scheme = 0`. Ensure the payment form includes:

```html
<!-- In payment form view -->
<input type="hidden" name="id_scheme" id="id_scheme" value="">
```

And in `payment.js`, populate it when the account is selected:
```javascript
$('#id_scheme').val(data.id_scheme);
```

---

### `admin/application/views/master/customer/form.php`
- **Remove** all static KYC fields (PAN, Voter ID, Ration Card, Aadhar hardcoded inputs).
- **Remove** KYC Bootstrap tab (was a tab panel inside the form).
- **Add** KYC modal trigger — the dynamic `kyc_modal.php` include.
- KYC data posts via hidden inputs `kyc_dynamic[{id_mas_kyc}][{attribute}]`.

---

### `admin/application/views/payment/form.php`
- Replace hardcoded inline KYC modal with shared `kyc_modal.php` include.
- KYC gate triggers before payment submit if rules are unmet.

---

### `admin/application/views/scheme/opening/form.php`
- Add `kyc_modal.php` include for scheme account opening KYC gate.

---

### `admin/application/views/layout/footer.php`
Load `kyc.js` and `kyc_modal.css` conditionally for the three affected URI segments:

```php
<?php if ($controller == 'account' || $controller == 'customer') { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/kyc_modal.css">
    <script src="<?php echo base_url(); ?>assets/js/kyc.js"></script>
<?php } ?>
<?php if ($controller == 'payment') { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/kyc_modal.css">
    <script src="<?php echo base_url(); ?>assets/js/kyc.js"></script>
<?php } ?>
```

---

### `admin/assets/js/customer.js`
Add KYC modal trigger on customer form submit — intercept submit, check KYC completeness, open modal if required documents are missing.

---

### `admin/assets/js/payment.js`
Add KYC gate before payment proceed — AJAX check for unmet rules; if any, open KYC modal before allowing payment save.

**Allow-pay gate** (commit `00cb3b6`): After KYC modal is completed and submitted, the payment "Proceed" button must be re-enabled. The gate works as:

```javascript
// Before payment submit:
function checkKycBeforePay() {
    // AJAX to payment/check_kyc — returns {kyc_complete: true/false}
    $.ajax({
        url: base_url + 'payment/check_kyc',
        data: { id_scheme_account: id_scheme_account, id_scheme: id_scheme },
        success: function(res) {
            if (res.kyc_complete) {
                proceedPayment(); // allow
            } else {
                trigger_dynamic_kyc_modal(id_scheme, id_scheme_account);
            }
        }
    });
}
```

---

### `admin/assets/js/scheme_account.js`
Add KYC gate on scheme account opening — same pattern as payment.js.

---

### `admin/assets/js/reports.js`
Add KYC report AJAX calls — load KYC status table (customer KYC verification status report).

---

### `admin/application/controllers/admin_reports.php`
Add KYC report endpoint — returns `kyc` table data with customer details + document status.

---

## Key Architecture Notes

### Rule Evaluation Logic (`kyc_model::get_unified_dynamic_kyc_data`)

| `kyc_mode` | `rules` value | Trigger |
|---|---|---|
| 0 (scheme-wise) | 0 | On scheme joining (account opening) |
| 0 (scheme-wise) | 1, type=0 | When cumulative payment amount ≥ threshold |
| 0 (scheme-wise) | 1, type=1 | When paid installments ≥ threshold |
| 1 (customer-wise) | 0 | On customer creation |
| 1 (customer-wise) | 1 | When customer's overall paid amount ≥ threshold |

### Logic Flag
- `logic = 0` → **All documents** in the group are required
- `logic = 1` → **At least one document** from the group is required

### KYC Status Values (`kyc.status`)
- `0` = Pending (submitted, awaiting manual review)
- `1` = Approved
- `2` = Auto-Verified (self integration + auto verification mode)
- `3` = Rejected

### Image Storage
Images are stored at: `assets/kyc/{id_mas_kyc}/{id_customer}/{attribute}_{timestamp}.png`
Full URL constructed as: `{protocol}://{HTTP_HOST}/{subfolder}/assets/kyc/...`

### Base64 Image Flow
Frontend sends images as `data:image/{type};base64,{data}` in hidden inputs.
`_process_save_kyc` detects `strpos($data, 'base64')`, decodes and saves to disk.
No `$_FILES` upload — pure base64 POST.

---

## Verification

1. **Admin Settings → KYC** — confirm settings panel exists, save works without SQL error.
2. **KYC Master list** — confirm `/kyc` route shows document list (PAN, Aadhar, DL, Voter ID).
3. **Add KYC Rule** — add rule: scheme-wise, on payment, amount ≥ 1000, require Aadhar, logic=All.
4. **Customer form** — confirm old static KYC tabs are gone; KYC modal appears on submit if rule triggered.
5. **Submit KYC** — fill Aadhar number + upload front/back images; confirm save succeeds with `kyc` row inserted.
6. **Payment form** — with rule active and KYC pending, confirm payment is gated (modal appears).
7. **Reports → KYC** — confirm KYC status report loads customer document statuses.

---

## Known Issues Fixed in This Phase

| Issue | Commit | Fix |
|---|---|---|
| `Error 1054: Unknown column 'id_kyc_setting'` | `c1a66c1` | Corrected PK name to `id_kyc_settings` in `save_kyc_settings` |
| `URL.createObjectURL is not a function` | `c1a66c1` | Changed to `window.URL.createObjectURL(f)` in `kyc_tab.php` |
| Silent transaction rollback on customer save | `c1a66c1` | Removed `file_put_contents(print_r(base64_data))` debug calls from `_process_save_kyc` |
| `id_scheme` posting as `0` in payment KYC flow | `42f311f` | Added hidden `id_scheme` input to payment form; populated via JS on account select |
| KYC save succeeding (toast shows) but record not persisted | `42f311f` | Fixed transaction scope — `_process_save_kyc` was catching exceptions and silently rolling back |
| Dynamic digit validation not firing on second document type | `5ae5d4c` | Fixed event binding order in `kyc_tab.php` — moved validation bind inside `DOMContentLoaded` |
| Payment "Proceed" not re-enabled after KYC completion | `00cb3b6` | Added `checkKycBeforePay()` gate and re-enable logic in `payment.js` after KYC modal submit |
