# Recipe: Add Salesman Field to Bill Copy (NRTM-020)

## Metadata
- **Pattern ID**: PAT-UI-BILL-001
- **Severity**: P2 — Enhancement
- **Modules Affected**: Billing (Model + Print Views)
- **Auto-fixable**: Yes
- **NRTM Task**: NRTM-020

## Created By
- **Developer**: Antigravity AI
- **Client**: Navratna Jewellery
- **Date**: 2026-04-17

---

## Symptom
Bill Copy does not show the Salesman (Sales Employee). Only the Billed Employee (logged-in user who pressed Save) is shown.

---

## Root Cause Analysis

### Three different "employee" concepts in this system:
| Concept | DB Column | Who Sets It |
|---|---|---|
| Billed Employee | `ret_billing.created_by` | Auto-captured from login session — no form input |
| Estimation Header Sales Employee | `ret_estimation.created_by` | "Sales Employee" dropdown at top of estimation form |
| Per-item Sales Employee | `ret_estimation_items.item_emp_id` | Per-row "Sales Employee" on each tag row |

### Key controller fallback (admin_ret_estimation.php line 409):
```php
'item_emp_id' => ($estTag['item_emp_id'][$key] != ''
                    ? $estTag['item_emp_id'][$key]   // per-row if selected
                    : $addData['created_by'])          // header employee as fallback
```
→ `item_emp_id` ALWAYS has a value — if no per-row selection, it defaults to the header employee.

### Bug 1 — Wrong JOIN in model (`getOtherEstimateItemsDetails`):
```sql
-- WRONG: Always joins estimation header employee, ignores per-row
LEFT JOIN employee e on e.id_employee = esti.created_by

-- CORRECT: Joins per-row employee (which already falls back to header when not set)
LEFT JOIN employee e on e.id_employee = est_itms.item_emp_id
```

### Bug 2 — Single overwrite in view loop:
```php
// WRONG: Last item's employee always wins
foreach ($est_other_item['item_details'] as $items) {
    $esti_sales_emp = $items['esti_emp_name']; // last one wins
}
```

---

## Business Logic / Display Rules
1. Header Sales Employee only (no per-row selections) → `Salesman : KR` (one code)
2. All products same employee → `Salesman : KR` (deduplicated — shown once)
3. Products with different employees → `Salesman : KR, RV` (unique codes, comma-separated)
4. No employee at all → Salesman line hidden

### Format: **Employee Code only** (NOT name, NOT name/code)

---

## Files Changed

### 1. `admin/application/models/ret_billing_model.php`

**In `getOtherEstimateItemsDetails()` — SELECT (around line 1295):**
```sql
-- Add emp_code to SELECT:
e.firstname as esti_emp_name, e.emp_code as esti_emp_code, e.id_employee AS esti_emp_id
```

**JOIN (around line 1324):**
```sql
-- Before:
LEFT JOIN employee e on e.id_employee = esti.created_by
-- After:
LEFT JOIN employee e on e.id_employee = est_itms.item_emp_id
```

**PHP return array (around line 1506):**
```php
'esti_emp_name'   => $item['esti_emp_name'],
'esti_emp_code'   => $item['esti_emp_code'],   // ADD THIS
'esti_emp_id'     => $item['esti_emp_id'],
```

> ⚠️ Safety: The `e` alias is ONLY used for `esti_emp_*` fields. No other columns (weights, costs, taxes, designs) are affected. The other two queries (`old_metal_query`, `return_details_query`) use `esti.created_by` and are NOT changed.

---

### 2. Both print view files:
- `admin/application/views/billing/print/billing_soft_copy.php`
- `admin/application/views/billing/print/bill_format_2.php`

**In the item loop — replace single overwrite with deduplication:**
```php
$salesman_map = [];
foreach ($est_other_item['item_details'] as $items) {
    // Collect unique salesmen: keyed by emp_id to deduplicate
    if (!empty($items['esti_emp_code']) && !isset($salesman_map[$items['esti_emp_id']])) {
        $salesman_map[$items['esti_emp_id']] = $items['esti_emp_code'];
    }
    // ... rest of existing loop code unchanged
```

**Display block (near bottom, after Billed Emp section):**
```php
<?php if ($billing['bill_emp_name'] != '') { ?>
<div style="font-weight:bold;">
    <br>
    <?php echo 'Billed Emp : ' . $billing['bill_emp_name'] . '/' . $billing['bill_emp_code'] ?>
</div>
<?php } ?>
<?php
$salesman_display = implode(', ', $salesman_map ?? []);
if (!empty($salesman_display)) { ?>
<div style="font-weight:bold;">
    <?php echo 'Salesman : ' . $salesman_display ?>
</div>
<?php } ?>
```

---

## Expected Output on Bill Copy

```
Billed Emp : RAMANATHAN/KR001
Salesman : KR          ← single employee
Salesman : KR, RV      ← multiple unique employees
```

---

## Verification
1. Open bill: `http://<host>/index.php/admin_ret_billing/billing_invoice/<bill_id>`
2. Scroll to bottom — verify **Billed Emp** and **Salesman** lines appear
3. Bill with all same estimation employee → one code shown
4. Bill with different per-item employees → codes comma-separated, deduplicated
5. Bill with no employee assigned → Salesman line hidden
6. `php -l` on all 3 files → No syntax errors

---

## Notes
- `Billed Emp` = `ret_billing.created_by` (auto, session) — always one person
- `Salesman` = `ret_estimation_items.item_emp_id` with fallback to `ret_estimation.created_by`
- `$esti_sales_emp` / `$esti_sales_id` variables are still declared at view top (legacy) — safe
