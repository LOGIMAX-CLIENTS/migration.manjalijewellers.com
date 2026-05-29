# Bug Execution Plan

## Bug ID: BIL-CLT02

## Module: Billing

## Category: Track B (Business Logic)

## Status: ⏳ In Progress

---

## 1. Business Rule (Plain English)

Billing discount validation checks three limits before allowing a discount:

1. **Bill discount check** (`disc_status`) — is the discount below the metal-rate-based limit? Computed by `DiscountValidationForBill()`.
2. **MC/VA discount limit** (`mc_va_disc_status`) — is the discount within the MC/VA ceiling? Computed by `discount_validation_for_salebill()`.
3. **Employee discount limit** (`emp_limit`) — can the logged-in employee give this much discount?

**Requested change:** Each of these three checks must have an independent ON/OFF toggle in Retail Settings. When a toggle is **OFF (0)**, that check's status is forced to `true` (bypassed). When it is **ON (1)**, the status is computed as normal. Whether the user then gets an **OTP prompt or a hard toaster block** is decided entirely by the **employee master** (`otp_emp_dis_approval`, `otp_mcva_dis_approval`).

---

## 2. New Settings

| DB Key                       | Default | Controls                                                  |
| ---------------------------- | ------- | --------------------------------------------------------- |
| `enable_emp_disc_limit`      | `1`     | `emp_limit.status`                                        |
| `enable_mc_va_disc_limit`    | `1`     | `mc_va_disc_status.status`                                |
| `enable_disc_blw_metal_rate` | `1`     | `disc_status.status` (from `DiscountValidationForBill()`) |

> `1` = enforce (compute status normally) | `0` = bypass (force status to `true`)

---

## 3. Decision Logic After Change

```
On #disc_apply click:
──────────────────────────────────────────────────────────────
Read:
  enable_emp_disc_limit      (1 or 0)
  enable_mc_va_disc_limit    (1 or 0)
  enable_disc_blw_metal_rate (1 or 0)

Compute statuses (existing functions, unchanged):
  disc_status         = DiscountValidationForBill();
  mc_va_disc_status   = discount_validation_for_salebill();
  emp_limit           = { computed from disc_limit vs summary_discount_amt }

Step A — Apply toggles (force status true if setting is off):
  if (enable_disc_blw_metal_rate == 0)  → disc_status.status = true
  if (enable_mc_va_disc_limit == 0)     → mc_va_disc_status.status = true
  if (enable_emp_disc_limit == 0)       → emp_limit.status = true

Step B — Single unified condition (old outer gate removed):
  if (
    disc_status.status == false ||
    mc_va_disc_status.status == false ||
    emp_limit.status == false
  ) {
    // INNER ROUTING — employee master decides OTP or toaster:
    if (emp_limit exceeded && otp_emp_dis_approval == 1 && ...)  → otp_confirmation()
    else if (mc_va exceeded && otp_mcva_dis_approval == 1 && ...) → otp_confirmation()
    else → $.toaster(danger)  [BLOCKED, no OTP configured]
  } else {
    calculateSaleBillRowTotal();  // all limits pass → apply discount
  }
──────────────────────────────────────────────────────────────
```

> **Key removal:** The old `&& disc_blw_metal_rate == 0` outer gate is **deleted**. The three status overrides in Step A now handle all cases cleanly without a separate gate.

---

## 4. Cross-Condition Scenarios

| `emp` | `mc_va` | `disc_blw` | Situation                 | Outcome                                          |
| ----- | ------- | ---------- | ------------------------- | ------------------------------------------------ |
| `1`   | `1`     | `1`        | Exceeds emp limit         | OTP via `otp_emp_dis_approval`, else toaster     |
| `1`   | `1`     | `1`        | Exceeds MC/VA limit       | OTP via `otp_mcva_dis_approval`, else toaster    |
| `0`   | `1`     | `1`        | Exceeds emp limit         | emp forced true → only MC/VA check fires         |
| `1`   | `0`     | `1`        | Exceeds MC/VA limit       | MC/VA forced true → only emp check fires         |
| `1`   | `0`     | `1`        | MC/VA off, emp exceeds    | MC/VA forced true; OTP via emp approval          |
| `0`   | `0`     | `0`        | All off                   | all forced true → discount applies freely        |
| `1`   | `1`     | `0`        | Disc below metal rate off | disc_status forced true; emp/MC/VA still enforce |

---

## 5. Files to Modify (5 files)

| #   | File                      | Change                                                                                 |
| --- | ------------------------- | -------------------------------------------------------------------------------------- |
| 1   | `ret_settings` DB         | `INSERT` 3 new keys (default `1`)                                                      |
| 2   | `retail_setting/form.php` | Add 3 dropdowns (Yes = 1 / No = 0)                                                     |
| 3   | `ret_billing_model.php`   | Fetch 3 new settings in `get_empty_record()`, add to `$emptydata[]`                    |
| 4   | `billing/form.php`        | Replace old `#disc_blw_metal_rate` hidden input (L332); add 3 new hidden inputs        |
| 5   | `ret_billing.js`          | Replace old variable reads; add Step A overrides; remove `&& disc_blw_metal_rate == 0` |

> Controller `admin_ret_billing.php` (L251) also fetches `disc_blw_metal_rate` — update to fetch `enable_disc_blw_metal_rate` instead.

---

## 6. JS Changes (ret_billing.js)

**Replace old variable read (L6699):**

```js
// BEFORE
var disc_blw_metal_rate = parseInt($("#disc_blw_metal_rate").val());

// AFTER (BIL-CLT02)
var enable_disc_blw_metal_rate =
    parseInt($("#enable_disc_blw_metal_rate").val()) || 1;
var enable_mc_va_disc_limit =
    parseInt($("#enable_mc_va_disc_limit").val()) || 1;
var enable_emp_disc_limit = parseInt($("#enable_emp_disc_limit").val()) || 1;
```

**Add Step A overrides after emp_limit is computed (~after L6727):**

```js
// BIL-CLT02: Force statuses to true when the corresponding setting is disabled
if (enable_disc_blw_metal_rate == 0) {
    disc_status.status = true;
}
if (enable_mc_va_disc_limit == 0) {
    mc_va_disc_status.status = true;
}
if (enable_emp_disc_limit == 0) {
    emp_limit.status = true;
}
```

**Replace the outer gate (L6729-6733):**

```js
// BEFORE
if (
  (disc_status.status == false ||
   mc_va_disc_status.status == false ||
   emp_limit.status == false) && disc_blw_metal_rate == 0
) {

// AFTER
if (
  disc_status.status == false ||
  mc_va_disc_status.status == false ||
  emp_limit.status == false
) {
```

---

## 7. DB SQL

```sql
-- BIL-CLT02: Three granular discount limit toggles
INSERT INTO ret_settings (name, value, created_on) VALUES
  ('enable_emp_disc_limit',      '1', NOW()),
  ('enable_mc_va_disc_limit',    '1', NOW()),
  ('enable_disc_blw_metal_rate', '1', NOW());
```

---

## 8. Rollback

- **DB:** `DELETE FROM ret_settings WHERE name IN ('enable_emp_disc_limit','enable_mc_va_disc_limit','enable_disc_blw_metal_rate');`
- **Code:** Revert `ret_billing.js`, `billing/form.php`, `admin_ret_billing.php`, `retail_setting/form.php`, `ret_billing_model.php`.
