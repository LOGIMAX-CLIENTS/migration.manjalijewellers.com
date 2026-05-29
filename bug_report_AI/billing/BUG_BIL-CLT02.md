## BIL-CLT02 — Granular Discount Validation Limit Toggles

| Field             | Value                                                            |
| ----------------- | ---------------------------------------------------------------- |
| **Severity**      | P2 — Minor (no data loss; validation bypass is admin-controlled) |
| **Track**         | B (Business Logic)                                               |
| **Category**      | Logic — Missing configurable business rules                      |
| **Sprint**        | Sprint 2 (next sprint — no active production breakage)           |
| **Pattern Match** | None (novel feature request — no existing PAT-\* applies)        |
| **Module Brain**  | ✅ Ready (`knowledge_brain/Billing/MODULE_BRAIN.md` exists)      |
| **Reporter**      | Client                                                           |
| **Source**        | CLT (Client request)                                             |
| **GitHub Issue**  | _TBD — see below_                                                |

---

### Description

The billing discount validation currently enforces three checks — employee discount limit, MC/VA discount limit, and disc-below-metal-rate gate — as a single all-or-nothing block. There is no way to selectively enable or disable individual limit checks from the retail admin settings.

The client requires **three independent toggles** in Retail Settings to control each check separately.

---

### Business Rule Being Implemented

Each toggle (`1` = enforce, `0` = bypass):

| Toggle                       | Controls                    | When `0`                                 |
| ---------------------------- | --------------------------- | ---------------------------------------- |
| `enable_emp_disc_limit`      | Employee discount limit     | `emp_limit.status` forced `true`         |
| `enable_mc_va_disc_limit`    | MC/VA discount limit        | `mc_va_disc_status.status` forced `true` |
| `enable_disc_blw_metal_rate` | Disc-below-metal-rate check | `disc_status.status` forced `true`       |

OTP vs. toaster is still decided by employee master (`otp_emp_dis_approval`, `otp_mcva_dis_approval`).

---

### Steps to Reproduce (Current Behavior)

1. Open Billing → select a customer → add items
2. In Total Summary → enter a discount that exceeds the employee's disc limit
3. Click Apply Discount
4. OTP prompt appears (because `otp_emp_dis_approval = 1`)
5. **No way to bypass this at admin/settings level per limit type**

### Expected Behavior

Admin can set `enable_emp_disc_limit = 0` in Retail Settings → employee limit check is skipped entirely, discount applies without OTP or toaster.

### Actual Behavior

All three checks are hardcoded active. No per-limit toggle exists.

---

### Files to Modify

1. `ret_settings` DB — INSERT 3 keys (default `1`)
2. `admin/application/views/settings/retail_setting/form.php`
3. `admin/application/models/ret_billing_model.php`
4. `admin/application/controllers/admin_ret_billing.php`
5. `admin/application/views/billing/form.php`
6. `admin/assets/js/ret_billing.js`

Full plan: `bug_report_AI/billing/BIL_CLT02_EXECUTION_PLAN.md`
## BIL-CLT02 — Cash Abstract Missing Bill Split (Home Bill / Estimate-to-Bill) Transactions

| Field | Value |
|---|---|
| **Severity** | P1 — Major |
| **Track** | A (System/Architecture) |
| **Category** | Logic — Wrong WHERE filter on `item_type` |
| **Sprint** | Sprint 1 |
| **Pattern Match** | PAT-QRY-003 (Missing WHERE Scope Filter — closest match; novel variant) |
| **Module Brain** | ✅ Ready (`knowledge_brain/Billing/MODULE_BRAIN.md`) |
| **Reporter** | Internal / Developer |
| **Source** | Client (CLT) |
| **Date Reported** | 2026-03-05 |

---

### Bug Summary

When a **Home Bill type estimate** is converted to a **Bill Split**, the resulting split transaction does not appear in the **Cash Abstract Report**. The transaction correctly appears in the **Home Bill Report** but is invisible in Cash Abstract.

---

### Root Cause (Reporter's Analysis — Confirmed for Investigation)

During Bill Split save, **two split items** are created:
1. **Item 1** → Saved as a full (complete) sale — `item_type` is set correctly (suspected `2`).
2. **Item 2** → Saved as a partial sale — `item_type` is set to `0` (incorrect / not set).

The **Cash Abstract Report SQL query** has a `WHERE item_type = 2` condition. Because Item 2's `item_type = 0`, it is excluded from the report.

**Root cause**: The Bill Split save logic fails to assign `item_type = 2` to the second (partial) split item when the source is a Home Bill type estimate.

---

### Steps to Reproduce

1. Create a **Home Bill type estimate** with at least two items.
2. Open the **Bill Split** form for this estimate.
3. Split the bill into two parts — ensure there are at least 2 line items.
4. Save the Bill Split.
5. Open the **Home Bill Report** → Transaction appears ✅.
6. Open the **Cash Abstract Report** → Transaction **missing** ❌.

---

### Expected Behavior

Both split items (full sale + partial sale) should appear in the **Cash Abstract Report** after a Home Bill type estimate is converted to Bill Split.

---

### Actual Behavior

The second split item (partial sale) does not appear in the Cash Abstract Report because its `item_type` is saved as `0` instead of the expected value (`2`). The Cash Abstract Report query filters with `WHERE item_type = 2`, so it is excluded.

---

### Data Flow to Investigate

```
Bill Split form (JS)
  → AJAX payload (item_type field per line item)
  → PHP Controller (bill split save case)
  → ret_billing_model.php (save/insert for split items)
  → DB column: item_type (on bill items table)
  → Cash Abstract Report query (WHERE item_type = 2)
```

### Files to Check

| File | Concern |
|---|---|
| `admin/assets/js/ret_billing.js` | Does JS send `item_type` for all split items? |
| `admin/application/controllers/admin_ret_billing.php` | Does the bill split save case set `item_type` for each item? |
| `admin/application/models/ret_billing_model.php` | Does the model correctly persist `item_type` for partial sale items? |
| Cash Abstract Report query (controller/model) | Confirm `WHERE item_type = 2` filter location |

---

### Evidence

- Reporter confirmed: second split item has `item_type = 0` in DB after save.
- Cash Abstract query confirmed to filter `item_type = 2`.

---

### Fix Strategy (for `/fix-architecture-bug`)

**Do NOT change the Cash Abstract WHERE condition.** The filter `WHERE item_type = 2` is correct business logic.

**Fix target**: The Bill Split save code must assign `item_type = 2` to **all** resulting split items (including the partial/second item), not just the first one.

Trace the save path for `case 'bill_split'` (or equivalent) in the controller and ensure `item_type` is set to the correct value for every inserted split item row.

---

### Acknowledgement SLA

**Source**: Client → SLA ≤ 2 hours  
**Status**: ✅ Triaged — Sprint 1  
**Message**: "Bug BIL-CLT02 received. Severity: P1. Target: ≤ 3 days (Sprint 1)."
