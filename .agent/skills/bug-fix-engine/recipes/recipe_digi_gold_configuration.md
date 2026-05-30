# Recipe: Digi Gold Configuration — Phase I

## Metadata
- **Pattern ID**: PAT-CHIT-DIGI-001
- **Severity**: HIGH
- **Modules Affected**: Scheme Master, Payment, Account Closing, Mobile App API, Admin App API, DigiGold Dashboard
- **Auto-fixable**: No — requires DB migration + new files + multi-layer code changes
- **Source Commits**:
  - `b074fe5` — Digi Gold conf phase I (34+ files)
  - `42f311f` — Digi gold closing issue fixed (admin_manage benefit logic)
  - `cb93639` — Pre-matured benefit disabled (admin_manage lines 883–885 commented)

## Client Scope
- **Applies to**: All chit/payment clients that want to offer a Digi Gold savings plan
- **Reason**: Clients need a day-based gold savings product ("Digi Gold") distinct from standard installment schemes. Requires new scheme flags, new payment columns, new app-facing models/controllers, benefit chart infrastructure, and a specialized closing flow.

## Created By
- **Developer**: Antigravity
- **Client**: dineshjewellery.in
- **Date**: 2026-04-27

---

## What is Digi Gold?

A **day-based gold savings plan** where customers save any amount daily within a configured min/max range. Key differences from standard schemes:
- Duration is in **days** (`total_days_to_pay`) not installments
- Each payment converts amount → gold weight at today's live rate
- Benefit is calculated on accumulated weight over time (% per annum or fixed)
- Maturity is `start_date + total_days_to_pay`
- Closing balance = `total saved weight + digi benefit weight`

---

## Database Migration

### Step 1 — Add columns to `scheme` table

```sql
ALTER TABLE scheme
  ADD COLUMN is_digi               TINYINT(1)  NOT NULL DEFAULT 0
    COMMENT '1 = This is a Digi Gold scheme',
  ADD COLUMN total_days_to_pay     INT(11)     DEFAULT NULL
    COMMENT 'Total days allowed to make payments (maturity period in days)',
  ADD COLUMN digi_target           TINYINT(1)  NOT NULL DEFAULT 0
    COMMENT '1 = Customer must set a savings target',
  ADD COLUMN digi_target_split_unit TINYINT(1) DEFAULT NULL
    COMMENT '1=Daily, 2=Weekly, 3=Monthly target split',
  ADD COLUMN key_benifits_description TEXT      DEFAULT NULL
    COMMENT 'HTML/text key benefits shown in mobile app';
```

### Step 2 — Add columns to `payment` table

```sql
ALTER TABLE payment
  ADD COLUMN saved_benefits     DECIMAL(10,4) DEFAULT NULL
    COMMENT 'Metal weight benefit saved at time of this payment (for Digi Gold)',
  ADD COLUMN saved_benefit_amt  DECIMAL(10,2) DEFAULT NULL
    COMMENT 'Amount equivalent of saved_benefits at payment date rate';
```

### Step 3 — Add column to `scheme_account` table

```sql
ALTER TABLE scheme_account
  ADD COLUMN dg_target_value_wgt DECIMAL(10,4) DEFAULT NULL
    COMMENT 'Customer-set Digi Gold weight target';
```

### Step 4 — `scheme_benefit_deduct_settings` table (benefit chart)

Used by both `apply_benefit_by_chart = 1` (benefit chart) and `apply_debit_on_preclose = 1` (closing deduction chart). **Must already exist** — if not, create:

```sql
CREATE TABLE IF NOT EXISTS `scheme_benefit_deduct_settings` (
  `id`               INT(11)     NOT NULL AUTO_INCREMENT,
  `id_scheme`        INT(11)     NOT NULL,
  `installment_from` INT(11)     NOT NULL COMMENT 'Days from (or installment from)',
  `installment_to`   INT(11)     NOT NULL COMMENT 'Days to (or installment to)',
  `interest_type`    TINYINT(1)  NOT NULL DEFAULT 0 COMMENT '0=%, 1=INR',
  `interest_value`   DECIMAL(10,2) NOT NULL DEFAULT 0,
  `created_by`       INT(11)     DEFAULT NULL,
  `date_add`         DATETIME    DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_scheme` (`id_scheme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Separate table for closing deduction chart:
CREATE TABLE IF NOT EXISTS `scheme_debit_settings` (
  `id`               INT(11)     NOT NULL AUTO_INCREMENT,
  `id_scheme`        INT(11)     NOT NULL,
  `installment_from` INT(11)     NOT NULL,
  `installment_to`   INT(11)     NOT NULL,
  `deduction_type`   TINYINT(1)  NOT NULL DEFAULT 0 COMMENT '0=%, 1=INR',
  `deduction_value`  DECIMAL(10,2) NOT NULL DEFAULT 0,
  `created_by`       INT(11)     DEFAULT NULL,
  `date_add`         DATETIME    DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_id_scheme` (`id_scheme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Step 5 — Verify `scheme` table has `apply_benefit_by_chart` and `apply_debit_on_preclose`

```sql
SHOW COLUMNS FROM scheme LIKE 'apply_benefit_by_chart';
SHOW COLUMNS FROM scheme LIKE 'apply_debit_on_preclose';

-- If missing:
ALTER TABLE scheme
  ADD COLUMN apply_benefit_by_chart  TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN apply_debit_on_preclose TINYINT(1) NOT NULL DEFAULT 0;
```

---

## New Files (create/copy from dineshjewellery.in)

| File | Purpose |
|---|---|
| `admin/application/models/digigold_modal.php` | Admin Digi Gold model: scheme settings, account data, benefit calculation, pay-dues logic |
| `application/controllers/digigold.php` | Mobile/API Digi Gold controller: customer dashboard, join, pay, benefit display |
| `application/models/digigold_modal.php` | Mobile/API Digi Gold model (same methods, adjusted for API context) |

---

## Modified Files

### `admin/application/controllers/admin_scheme.php`

**Purpose**: Save `is_digi`, `total_days_to_pay`, `key_benifits_description` + validate uniqueness + save benefit/debit charts.

In both `Add` and `Edit` `$sch_info` arrays, add:

```php
'is_digi'               => (isset($sch_data['is_digi']) ? $sch_data['is_digi'] : 0),
'total_days_to_pay'     => (isset($sch_data['total_days_to_pay']) && $sch_data['total_days_to_pay'] > 0
                              ? $sch_data['total_days_to_pay'] : NULL),
'key_benifits_description' => (isset($sch_data['key_benifits_description'])
                              ? $sch_data['key_benifits_description'] : NULL),
'apply_benefit_by_chart'   => (isset($sch_data['apply_benefit_by_chart'])
                              ? $sch_data['apply_benefit_by_chart'] : 0),
'apply_debit_on_preclose'  => (isset($sch_data['apply_debit_on_preclose'])
                              ? $sch_data['apply_debit_on_preclose'] : 0),
```

**After building `$sch_info`**, add is_digi uniqueness check (before `trans_begin`):

```php
// Validate: only ONE scheme per commodity can have is_digi = 1
if (isset($sch_data['is_digi']) && $sch_data['is_digi'] == 1
    && isset($sch_data['id_metal']) && $sch_data['id_metal'] > 0
) {
    // For Add: exclude_id = 0   |   For Edit: exclude_id = $id
    $digi_conflict = $this->$model->check_digi_scheme_by_metal($sch_data['id_metal'], $exclude_id);
    if ($digi_conflict) {
        $this->session->set_flashdata('sch_info', [
            'message' => 'Cannot set Is Digi — scheme "'.$digi_conflict['scheme_name'].'" already has Digi for this commodity.',
            'class'   => 'danger',
            'title'   => 'Is Digi Conflict'
        ]);
        redirect('scheme/add');   // or 'scheme/edit/'.$id
        return;
    }
}
```

**After save** (inside `if ($res['status'])` / success block), save benefit and preclose charts:

```php
// Benefit chart (days-based interest slabs)
if ($sch_data['apply_benefit_by_chart'] == 1 && $sch_data['apply_debit_on_preclose'] == 0) {
    // On Edit: $this->$model->delete_benfit_rdeduct($id);
    foreach ($_POST['installmentchart'] as $chartdata) {
        $this->$model->insertData([
            'id_scheme'        => $res['id_scheme'],
            'installment_from' => $chartdata['installment_from'],
            'installment_to'   => $chartdata['installment_to'],
            'interest_type'    => $chartdata['interest_type'],
            'interest_value'   => $chartdata['interest_value'],
            'created_by'       => $this->session->userdata('uid'),
            'date_add'         => date('Y-m-d H:i:s'),
        ], 'scheme_benefit_deduct_settings');
    }
}

// Preclose deduction chart
if ($sch_data['apply_benefit_by_chart'] == 0 && $sch_data['apply_debit_on_preclose'] == 1) {
    // On Edit: $this->$model->delete_benfit_rdeduct_preclose($id);
    foreach ($_POST['installmentpreclosechart'] as $chartdata) {
        $this->$model->insertDatas([
            'id_scheme'        => $res['id_scheme'],
            'installment_from' => $chartdata['installment_from'],
            'installment_to'   => $chartdata['installment_to'],
            'deduction_type'   => $chartdata['deduction_type'],
            'deduction_value'  => $chartdata['deduction_value'],
            'created_by'       => $this->session->userdata('uid'),
            'date_add'         => date('Y-m-d H:i:s'),
        ], 'scheme_debit_settings');
    }
}
```

---

### `admin/application/models/scheme_model.php`

Add `is_digi`, `total_days_to_pay`, `key_benifits_description` to the `get_scheme()` SELECT.

Add `empty_record()` defaults:

```php
'is_digi'               => 0,
'total_days_to_pay'     => NULL,
'key_benifits_description' => NULL,
'apply_benefit_by_chart'   => 0,
'apply_debit_on_preclose'  => 0,
```

Add new methods:

```php
// Check if another scheme in the same commodity already has is_digi=1
// exclude_id: 0 for Add, current $id for Edit
public function check_digi_scheme_by_metal($id_metal, $exclude_id) {
    $sql = "SELECT id_scheme, scheme_name FROM scheme
            WHERE is_digi = 1 AND id_metal = $id_metal AND id_scheme != $exclude_id LIMIT 1";
    $result = $this->db->query($sql)->row_array();
    return $result ?: false;
}

// Get benefit chart rows
public function get_benfit_rdeduct_data($id_scheme) { ... }
public function get_benfit_rdeduct_preclose__data($id_scheme) { ... }

// Delete chart rows (for Edit/re-save)
public function delete_benfit_rdeduct($id_scheme) { ... }
public function delete_benfit_rdeduct_preclose($id_scheme) { ... }
```

---

### `admin/application/models/account_model.php`

In `get_close_account()` SELECT (the account closing query), add:

```sql
IFNULL(SUM(p.saved_benefits),0)    as dg_saved_benefit_weight,
IFNULL(SUM(p.saved_benefit_amt),0) as dg_saved_benefit_amount,
DATEDIFF(CURDATE(),date(sa.start_date)) joined_date_diff,
s.is_digi,
s.interest as sch_int_setting,
```

---

### `admin/application/controllers/admin_manage.php`

In the account closing `View` case, after the closing balance calculation:

```php
$dg_saved_benefit_amount = 0;
$dg_saved_benefit_weight = 0;

if ($account['is_digi'] == 1
    && $account['apply_benefit_by_chart'] == 1
    && $account['sch_int_setting'] == 1
) {
    if ($account['maturity_date'] <= date('Y-m-d')) {
        // Full matured — apply saved benefit
        $dg_saved_benefit_amount = $account['dg_saved_benefit_amount'];
        $dg_saved_benefit_weight = formatMetalWeight($account['dg_saved_benefit_weight']);
        $acc_int_amount          = $dg_saved_benefit_amount;
    } else {
        // Pre-matured — NO benefit applied (intentional — commit cb93639)
        // $dg_saved_benefit_amount = $account['dg_saved_benefit_amount'];
        // $dg_saved_benefit_weight = formatMetalWeight($account['dg_saved_benefit_weight']);
        // $acc_int_amount          = $dg_saved_benefit_amount;
    }
}

// Pass to $data['account']
$data['account']['dg_saved_benefit_amount'] = round($dg_saved_benefit_amount, 2);
$data['account']['dg_saved_benefit_weight'] = $dg_saved_benefit_weight;
$data['account']['is_digi']                 = $account['is_digi'];
$data['account']['apply_benefit_by_chart']  = $account['apply_benefit_by_chart'];
$data['account']['joined_date_diff']        = (!empty($account['joined_date_diff'])
                                                ? $account['joined_date_diff'] : 1);
```

**For Digi Gold, override closing balance**:

```php
if ($account['is_digi'] == 1) {
    $data['account']['closing_balance'] = formatMetalWeight(
        $account['paid_weight'] + $dg_saved_benefit_weight
    );
    $data['account']['closing_amount'] = formatMetalWeight(
        $account['paid_amount'] + $dg_saved_benefit_amount
    );
}
```

---

### `admin/application/models/payment_model.php`

In `get_paymentContent` / `getSchemeAccountData` AJAX response, add Digi Gold fields:

```php
'is_digi'                   => $record->is_digi,
'dg_benefit_type'           => $record->dg_benefit_type ?? 0,
'dg_benefit_value'          => $record->dg_benefit_value ?? 0,
'dg_benefit_content'        => $record->dg_benefit_content ?? '',
```

In payment save (`save_payment`) for Digi Gold accounts, compute and store:

```php
if ($record->is_digi == 1 && $digi_benefit_rate > 0) {
    $saved_benefit_wgt = round(($metal_wgt * ($digi_benefit_rate / 100) * ($days_since_join / 365)), 4);
    $insert_data['saved_benefits']    = $saved_benefit_wgt;
    $insert_data['saved_benefit_amt'] = round($saved_benefit_wgt * $metal_rate, 2);
}
```

---

### `application/models/digigold_modal.php` (mobile API)

Key methods:

| Method | Purpose |
|---|---|
| `digiGold_settings()` | Get the active Digi Gold scheme config (`is_digi=1`) |
| `digiGold_account($id_customer)` | Get customer's active Digi Gold account + accumulated data |
| `getCusDigiData($data)` | Combined: settings + account + benefit chart + pay dues + target progress |
| `digi_payDues($acc)` | Returns `digi_allow_pay` (Y/N), min/max amount/weight, current rate |
| `get_digi_benefit($res)` | Looks up `scheme_benefit_deduct_settings` for the current day slab |
| `digi_benefit_chart_data($reached_day)` | Returns all benefit chart slabs with `is_current` flag |
| `getdigidata($data)` | Old-style combined data for chit wallet view |
| `updData($data, $field, $value, $table)` | Generic update (for setting target weight) |

---

### `admin/application/views/master/scheme/form.php`

Add Digi Gold section (after standard scheme fields):

```html
<!-- Is Digi Gold toggle -->
<input type="checkbox" name="sch[is_digi]" value="1" id="is_digi" <?= $sch['is_digi']==1 ? 'checked' : '' ?>>
<label>Is Digi Gold Scheme</label>

<!-- Show only when is_digi is checked -->
<div id="digi_fields" style="<?= $sch['is_digi']==1 ? '' : 'display:none' ?>">
    <input type="number" name="sch[total_days_to_pay]" value="<?= $sch['total_days_to_pay'] ?>">
    <textarea name="sch[key_benifits_description]"><?= $sch['key_benifits_description'] ?></textarea>
    <input type="checkbox" name="sch[digi_target]" value="1">
    <select name="sch[digi_target_split_unit]">...</select>
</div>

<!-- Benefit chart (shown when apply_benefit_by_chart=1) -->
<div id="benefit_chart_section">
    <!-- Dynamic rows: installment_from, installment_to, interest_type, interest_value -->
</div>
```

---

## Scheme Master Configuration Steps

When setting up Digi Gold for a client:

1. **Go to Scheme Master → Add Scheme**
2. Set `Scheme Type = Flexible (3)` with `Flexible Sch Type` appropriately
3. Set `Min Amount` / `Max Amount` (daily savings range)
4. ✅ Enable **"Is Digi Gold"** → enter `Total Days to Pay` (e.g. 365)
5. ✅ Enable **"Apply Benefit by Chart"** (if interest benefit on closing)
6. Add **Benefit Chart rows** (days_from → days_to → interest type → %)
7. Optionally enable **"Digi Target"** + set split unit
8. Save scheme

---

## Key Business Rules

| Rule | Behaviour |
|---|---|
| Only 1 Digi scheme per commodity | Enforced in `admin_scheme.php` → redirects with error if conflict |
| Benefit on full maturity only | `maturity_date <= today` → applies `dg_saved_benefit_amount` |
| Pre-matured closing | Benefit NOT applied (lines commented in `admin_manage.php`) |
| Digi closing balance | `paid_weight + dg_saved_benefit_weight` (not standard installment balance) |
| `sch_int_setting` | This is the `s.interest` column aliased — `1` = benefit interest enabled |
| `dg_saved_benefit_weight/amount` | Aggregated from `SUM(payment.saved_benefits)` and `SUM(payment.saved_benefit_amt)` |

---

## Verification

1. **Scheme Master** → Add scheme with `is_digi=1` → confirm saves without error
2. **Duplicate check** → try to set another scheme in same commodity as Digi → expect conflict flash
3. **Payment** → make a payment on Digi account → confirm `saved_benefits` and `saved_benefit_amt` stored in `payment` table
4. **Account Closing (matured)** → closing balance = `paid_weight + benefit_weight`
5. **Account Closing (pre-matured)** → benefit = 0, closing balance = `paid_weight` only
6. **Mobile App** → Digi Gold dashboard shows correct benefit slab and % progress
7. **SQL check**:

```sql
-- Verify saved benefits are stored:
SELECT id_payment, payment_amount, metal_weight, saved_benefits, saved_benefit_amt
FROM payment
WHERE id_scheme_account = <DIGI_ACCOUNT_ID>
ORDER BY date_payment;

-- Verify closing aggregation:
SELECT
    sa.id_scheme_account,
    IFNULL(SUM(p.saved_benefits), 0)    as dg_benefit_wgt,
    IFNULL(SUM(p.saved_benefit_amt), 0) as dg_benefit_amt,
    IFNULL(SUM(p.metal_weight), 0)      as paid_weight,
    s.is_digi
FROM scheme_account sa
JOIN scheme s ON s.id_scheme = sa.id_scheme
LEFT JOIN payment p ON p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1
WHERE s.is_digi = 1
GROUP BY sa.id_scheme_account;
```

---

## Known Issues Fixed (commits 42f311f / cb93639)

| Issue | Fix |
|---|---|
| Pre-matured Digi accounts showed full maturity benefit | Commented out the 3 benefit assignment lines in `admin_manage.php:883–885` |
| `is_weight` flag calculation wrong for `flexible_sch_type=2` with `wgt_convert=2` | Fixed the nested condition in admin_manage closing view |
| KYC issues resolved in same commit | See `recipe_dynamic_kyc_phase_I.md` for KYC-specific fixes |

---

## Phase II — Mobile API Integration & Bug Fixes (2026-04-28)

### Source Commits
- `feature/digigold` — multiple changes to `mobileapi_model.php`, `digigold_modal.php`, `mobile_api.php`, `login_model.php`, `payment_modal.php`

---

### Phase II — Modified Files

#### `application/models/mobileapi_model.php`

**1. Expose Digi Gold toggle + weight config to mobile app**

In the company settings query (`get_company_settings` / master info), add to SELECT:

```sql
cs.enable_digi_gold as show_digi,
cs.metal_wgt_decimal,
cs.metal_wgt_roundoff
```

This lets the mobile app read `show_digi` to conditionally show/hide the Digi Gold section and use `metal_wgt_decimal` / `metal_wgt_roundoff` for client-side formatting.

---

**2. Filter Digi Gold from regular scheme classification lists**

Both `get_classifications()` and `get_classifications_by_costcenter()` must exclude Digi Gold categories.

Before (wrong — shows Digi Gold categories in regular chit list):
```sql
SELECT id_classification, classification_name, description,
       concat('<IMG_PATH>','',sc.logo) as logo
FROM sch_classify sc
WHERE active = 1
```

After (correct):
```sql
SELECT sc.id_classification, sc.classification_name, sc.description,
       concat('<IMG_PATH>','',sc.logo) as logo
FROM sch_classify sc
LEFT JOIN scheme s ON s.id_classification = sc.id_classification
WHERE sc.active = 1 AND s.is_digi = 0
GROUP BY sc.id_classification
```

> ⚠️ Apply this fix to **both** classification methods — they have separate implementations.

---

**3. Filter Digi Gold from `countSchemes()`**

Before:
```sql
SELECT count(id_scheme) as schemes
FROM scheme_account
left join scheme s on s.id_scheme = sa.id_scheme
WHERE sa.active = 1 AND id_customer = '$id_customer'
```

After:
```sql
SELECT count(sa.id_scheme) as schemes
FROM scheme_account sa
left join scheme s on s.id_scheme = sa.id_scheme
WHERE s.is_digi = 0 AND sa.active = 1 AND id_customer = '$id_customer'
```

> Without this fix, Digi Gold accounts inflate the regular scheme count on the mobile dashboard.

---

**4. `formatMetalWeight()` rollout — all weight fields in Mobile API**

Remove all `FORMAT(..., 3)` from SQL queries. Fetch raw float values, then apply `formatMetalWeight()` in PHP after the query.

**Pattern A — SQL fix + PHP loop (for result arrays):**
```php
// In SQL: replace FORMAT(IFNULL(col,0),3) with IFNULL(col,0)
// After query:
foreach ($rows as &$row) {
    $row['total_weight']     = formatMetalWeight($row['total_weight']);
    $row['cur_total_weight'] = formatMetalWeight($row['cur_total_weight']);
    $row['eligible_weight']  = formatMetalWeight($row['eligible_weight']);
}
unset($row);
```

**Pattern B — Response array field wrapping:**
```php
'total_paid_weight'    => formatMetalWeight($record->total_paid_weight),
'max_weight'           => formatMetalWeight($record->max_weight),
'min_weight'           => formatMetalWeight($record->min_weight),
'current_total_weight' => formatMetalWeight($record->current_total_weight),
'eligible_weight'      => formatMetalWeight($record->max_weight - $record->current_total_weight),
```

**Full list of fields to migrate across all account/scheme methods:**

| Method / Context | Fields to wrap |
|---|---|
| `get_schemeaccounts_by_customer()` | `total_weight`, `cur_total_weight`, `eligible_weight` |
| `get_schemeaccount_detail()` (v1) | `max_weight`, `current_total_weight`, `total_paid_weight`, `eligible_weight` |
| `get_schemeaccount_detail()` (v2) | `max_weight`, `min_weight`, `current_total_weight`, `total_paid_weight` |
| `get_schemeaccount_detail()` (v3/closed) | `max_weight`, `min_weight`, `current_total_weight`, `total_paid_weight` |
| Payment history loop | `weight` per payment row |
| Dashboard `totalAmtWgt()` result | `total_weight` |
| Payment records list | `metal_weight` |

---

**5. `branch_settings()` — expose weight config at login**

In `login_model.php`, add `metal_wgt_decimal`, `metal_wgt_roundoff` to `branch_settings()` query:

```sql
SELECT is_kyc_required, branch_settings, branchWiseLogin,
       is_branchwise_cus_reg, branchwise_scheme, cost_center,
       metal_wgt_decimal, metal_wgt_roundoff
FROM chit_settings WHERE id_chit_settings = 1
```

> Required so the mobile app receives weight precision config at login time for session-level formatting.

---

#### `application/models/digigold_modal.php` (mobile/API)

**Bug 1 — Scheme account number format wrong**

Before (concatenated raw string):
```php
CONCAT(sa.start_year,'-',s.code,'-',IFNULL(sa.scheme_acc_number,'Not Allocated')) as scheme_acc_number
```

After (use allocated number directly):
```php
IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number
```

---

**Bug 2 — `formatMetalWeight()` missing on Digi account summary fields**

In `digiGold_account()`, after fetching `$data = $sql->row_array()`, add:

```php
if (!empty($data)) {
    $data['total_paid_weight']    = formatMetalWeight($data['total_paid_weight']);
    $data['total_saved_benefits'] = formatMetalWeight($data['total_saved_benefits']);
    $data['total_saved']          = formatMetalWeight($data['total_saved']);
}
```

---

**Bug 3 — `digi_benefit_chart_data()` had hardcoded `id_scheme = 13`**

This caused **all** Digi Gold accounts across all clients to show the same benefit chart (scheme 13 from the original dev client). Fix:

Before:
```php
public function digi_benefit_chart_data($reached_day) {
    $sql = $this->db->query("SELECT ... FROM scheme_benefit_deduct_settings
                             WHERE id_scheme = 13");
```

After:
```php
public function digi_benefit_chart_data($reached_day, $id_scheme) {
    $sql = $this->db->query("SELECT ... FROM scheme_benefit_deduct_settings
                             WHERE id_scheme = " . $id_scheme);
```

Update the **caller** in `getCusDigiData()`:
```php
$id_scheme = (!empty($digi['id_scheme']) && $digi['id_scheme'] > 0 ? $digi['id_scheme'] : '');
$digi['benefit_chart_data'] = $this->digi_benefit_chart_data($reached_day, $id_scheme);
```

> ⚠️ **Critical bug** — without this fix, every new client deployment shows the wrong benefit chart.

---

#### `application/models/payment_modal.php`

In `get_schemeByChit()`, add `s.interest` to the SELECT:

```sql
SELECT s.is_digi, s.interest, s.id_scheme, s.code, s.scheme_type, ...
```

> Required so the payment flow can check `interest == 1` to gate Digi Gold benefit calculation.

---

#### `application/controllers/mobile_api.php`

In the Cashfree/payment webhook handler, replace hardcoded `number_format(..., 3)` with `formatMetalWeight()` for Digi Gold payment fields:

```php
// Before:
$metal_wgt = number_format(($pay->amount / $pay->udf3), 3);
// ...
$dg_saved_benefit = number_format(($dg_saved_benefit_amt / $pay->udf3), 3);

// After:
$metal_wgt = formatMetalWeight(($pay->amount / $pay->udf3));
// ...
$dg_saved_benefit = formatMetalWeight(($dg_saved_benefit_amt / $pay->udf3));
```

---

### Phase II — Verification

```sql
-- 1. Confirm classifications exclude Digi Gold:
SELECT sc.id_classification, sc.classification_name
FROM sch_classify sc
LEFT JOIN scheme s ON s.id_classification = sc.id_classification
WHERE sc.active = 1 AND s.is_digi = 0
GROUP BY sc.id_classification;

-- 2. Confirm scheme count excludes Digi Gold:
SELECT count(sa.id_scheme) as schemes
FROM scheme_account sa
LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
WHERE s.is_digi = 0 AND sa.active = 1 AND id_customer = <CUSTOMER_ID>;

-- 3. Verify benefit chart uses correct scheme:
SELECT * FROM scheme_benefit_deduct_settings WHERE id_scheme = <DIGI_SCHEME_ID>;
```

**Mobile API smoke test:**
1. Call company settings endpoint → verify `show_digi`, `metal_wgt_decimal`, `metal_wgt_roundoff` present
2. Call scheme list endpoint → confirm Digi Gold category NOT in regular list
3. Call Digi Gold dashboard → confirm `total_paid_weight`, `total_saved_benefits` formatted correctly per config
4. Check benefit chart response → slabs must match the actual Digi scheme's `scheme_benefit_deduct_settings`, not scheme 13

### Phase II — Known Issues Fixed

| Issue | Fix |
|---|---|
| Benefit chart always showed scheme 13's data | `digi_benefit_chart_data()` now accepts and uses `$id_scheme` |
| Scheme account number showed `YEAR-CODE-NUMBER` raw string | Replaced `CONCAT(...)` with `IFNULL(sa.scheme_acc_number,'Not Allocated')` |
| Digi Gold categories appeared in regular chit classification list | Added `s.is_digi = 0` filter + `GROUP BY` to both classification queries |
| Digi Gold accounts counted in regular scheme count | Added `s.is_digi = 0` filter to `countSchemes()` |
| Weight fields returned as hardcoded 3dp in all Mobile API methods | Removed `FORMAT(...,3)` from SQL; applied `formatMetalWeight()` in PHP |
| `enable_digi_gold`, `metal_wgt_decimal/roundoff` not sent to mobile app | Added to company settings query and `branch_settings()` login query |
| Cashfree webhook computed Digi weight at hardcoded 3dp | Replaced `number_format(...,3)` with `formatMetalWeight()` in `mobile_api.php` |
