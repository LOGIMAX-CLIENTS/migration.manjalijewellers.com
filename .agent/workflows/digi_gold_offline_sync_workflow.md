# Digi Gold Offline Sync — Implementation Workflow

> **IMPORTANT:**
> This workflow documents all changes made across commits `6bb0e48` → `7094b72` → `b98db6e` → `8a6c6f7` in **sreekumaranjewellers**. Apply these to each new client project.

---

## Overview

These commits add **Digi Gold support to the offline sync pipeline**. The changes span **3 files**:

| # | File | Purpose |
|---|------|---------|
| 1 | `admin/application/controllers/sync_api.php` | Add digi gold fields to offline transaction & customer insert APIs |
| 2 | `application/models/registration_model.php` | Digi duplicate prevention, closing amount/weight, benefit fields sync |
| 3 | `admin/application/models/admin_settings_model.php` | Remove `goldrate_14ct` column from metal rate queries |

---

## Pre-Flight Checklist (per client)

Before applying changes, verify these DB prerequisites:

- [ ] `transaction` table has columns: `days`, `saved_benefits_wgt`, `saved_benefit_amt`, `benefit_value`, `benefit_type`, `is_digi`
- [ ] `customer_reg` table has columns: `is_digi`, `maturity_date`, `maturity_amount`, `maturity_weight`, `account_name`
- [ ] `scheme_account` table has columns: `closing_amount`, `closing_weight`
- [ ] `scheme` table has column: `is_digi`
- [ ] `payment` table has columns: `saved_benefits`, `saved_benefit_amt`, `benefit_value`, `benefit_type`
- [ ] `metal_rate` table — check if `goldrate_14ct` column exists (determines Step 7)

---

## Step 1: `sync_api.php` — `insertTransactions_post()`

**Location:** Inside the `$data = array(...)` in the `insertTransactions_post()` function, right after the `"receipt_no"` field and before the `"new_customer"` field.

**Add these 6 fields:**

```php
"days"              => $transaction->days,
"saved_benefits_wgt" => $transaction->saved_benefits,
"saved_benefit_amt" => $transaction->saved_benefit_amt,
"benefit_value"     => $transaction->benefit_value,
"benefit_type"      => $transaction->benefit_type,
"is_digi"           => $transaction->is_digi,
```

> **WARNING:** The DB column is `saved_benefits_wgt` (not `saved_benefits`). The source object property is `$transaction->saved_benefits`. Make sure the key name is `saved_benefits_wgt`.

---

## Step 2: `sync_api.php` — `insertCustomers_post()`

### 2a. Fix `account_name` source property

**Location:** Inside the `$data = array(...)` in `insertCustomers_post()`.

**Change:**
```diff
-"account_name"  => $cus->ac_name,
+"account_name"  => $cus->account_name,
```

### 2b. Add conditional closing amount/weight for Digi Gold

**Location:** Replace the `closing_amount` and `closing_weight` lines.

**Change:**
```diff
-"closing_amount"    => $cus->closing_amount,
-"closing_weight"    => $cus->closing_weight,
+"closing_amount"    => ($cus->is_digi == 1 ? $cus->maturity_amount : $cus->closing_amount),
+"closing_weight"    => ($cus->is_digi == 1 ? $cus->maturity_weight : $cus->closing_weight),
```

**Logic:** For Digi Gold accounts (`is_digi == 1`), the closing values come from `maturity_amount` / `maturity_weight` instead of the regular `closing_amount` / `closing_weight`.

### 2c. Add Digi Gold metadata fields

**Location:** After the `"ref_no"` field in the same `$data` array.

**Add:**
```php
"is_digi"         => $cus->is_digi,
"maturity_date"   => $cus->maturity_date,
```

> **NOTE:** `maturity_amount` and `maturity_weight` are NOT stored separately — they are already mapped into `closing_amount` and `closing_weight` via the conditional logic in Step 2b.

---

## Step 3: `registration_model.php` — `insExisAcByMobile()` — Digi Duplicate Prevention

**Location:** Inside the `insExisAcByMobile()` function, right BEFORE the `$records = array(...)` that builds the `scheme_account` insert data (after `if($id_scheme > 0){`).

**Add this block:**

```php
// Digi Gold duplicate prevention - skip if customer already has an active digi scheme
$sch_digi_check = $this->db->query("SELECT is_digi FROM scheme WHERE id_scheme = " . $id_scheme);
if ($sch_digi_check->num_rows() > 0 && $sch_digi_check->row()->is_digi == 1) {
    $digi_exists = $this->db->query(
        "SELECT sa.id_scheme_account FROM scheme_account sa
         LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
         WHERE s.is_digi = 1 AND sa.active = 1 AND sa.is_closed = 0
         AND sa.id_customer = " . $data['id_customer']
    );
    if ($digi_exists->num_rows() > 0) {
        continue; // Customer already has an active digi gold account, skip this entry
    }
}
```

**Why:** Prevents duplicate Digi Gold scheme accounts when a customer already has one active. The regular scheme sync doesn't need this check because regular schemes allow multiple accounts.

---

## Step 4: `registration_model.php` — `insExisAcByMobile()` — Account Name Fallback

**Location:** Inside the `$records = array(...)` that builds the `scheme_account` insert data.

**Change:**
```diff
-'account_name'  => $row->account_name,
+'account_name'  => ($row->account_name != '' || $row->account_name != NULL ? $row->account_name : $row->ac_name),
```

**Why:** Some offline records use `ac_name` instead of `account_name`. This fallback ensures the account name is always populated.

---

## Step 5: `registration_model.php` — `insExisAcByMobile()` — Add Closing Fields

**Location:** Inside the same `$records = array(...)`, after the `'fixed_wgt'` field and before the `'id_branch'` field.

**Add:**
```php
'closing_amount'  => $row->closing_amount,
'closing_weight'  => $row->closing_weight,
```

**Why:** These fields were previously not being synced from `customer_reg` to `scheme_account`, causing missing data for closed/maturing accounts.

---

## Step 6: `registration_model.php` — `syncPayData()` — Benefit Fields in Payment Insert

**Location:** Inside the `syncPayData()` function, in the `$records = array(...)` that builds the payment insert data. After the `'payment_type' => 'Offline'` line and before the `'is_offline'` line.

**Add:**
```php
'saved_benefits'    => $row->saved_benefits_wgt,
'saved_benefit_amt' => $row->saved_benefit_amt,
'benefit_value'     => $row->benefit_value,
'benefit_type'      => $row->benefit_type,
```

> **NOTE:** The source column is `saved_benefits_wgt` (from the `transaction` table) and maps to `saved_benefits` in the `payment` table.

---

## Step 7: `admin_settings_model.php` — Remove `goldrate_14ct`

**Location:** There are **2 occurrences** of `m.goldrate_14ct` in the metal rate SELECT queries. Remove both.

**Change (in both locations):**
```diff
 m.goldrate_22ct,
 m.goldrate_18ct,
-m.goldrate_14ct,
 m.platinum_1g,
```

> **WARNING:** This assumes the target client's `metal_rate` table does NOT have a `goldrate_14ct` column. Verify the DB schema first. If the column exists, skip this step.

---

## File-Level Change Summary

### `admin/application/controllers/sync_api.php`

| Function | What Changed |
|----------|-------------|
| `insertTransactions_post()` | Added 6 digi gold fields to `$data` array |
| `insertCustomers_post()` | Fixed `account_name` source property; added conditional closing logic for digi; added `is_digi` and `maturity_date` fields |

### `application/models/registration_model.php`

| Function | What Changed |
|----------|-------------|
| `insExisAcByMobile()` | Added digi duplicate prevention logic; added `account_name` fallback; added `closing_amount`/`closing_weight` to scheme_account insert |
| `syncPayData()` | Added 4 benefit fields to payment insert |

### `admin/application/models/admin_settings_model.php`

| Function | What Changed |
|----------|-------------|
| Metal rate queries (2 locations) | Removed `m.goldrate_14ct` from SELECT |
