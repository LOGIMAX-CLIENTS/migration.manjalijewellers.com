## PUR-INT01 — Duplicate po_ref_no / grn_ref_no Due to Race Condition

| Field | Value |
|---|---|
| Severity | P1 — Major |
| Track | A (System) |
| Category | 8 — Concurrency |
| Sprint | Sprint 1 |
| Pattern Match | None (Novel — candidate PAT-CON-001) |
| Module Brain | ✅ Ready |
| Reporter | Internal (developer) |
| Source | Internal observation |
| Sub-Module | GRN Entry / Supplier Bill Entry |
| Environment | Production + Staging |
| Frequency | Reproducible on concurrent access |

### Steps to Reproduce
1. Open two browser tabs to the Supplier Bill Entry form (or GRN Entry form)
2. Fill in valid data on both tabs
3. Click Save on both tabs as quickly as possible (within 1-2 seconds)
4. Observe `ret_purchase_order.po_ref_no` or `ret_grn_entry.grn_ref_no` — two rows with same value

### Expected Behavior
Each save should produce a unique, sequential `po_ref_no` / `grn_ref_no`, even under concurrent access.

### Actual Behavior
Both saves can produce the same ref number (e.g., `PM-00016` appears twice). Additionally, the old code produced **wrong prefixes** — a `grn_type=1` (Bill) entry was assigned a `PM-` prefix instead of `PU-`.

### Evidence — DB Query Results (2026-02-25)

Duplicate confirmed in `ret_grn_entry` on `arc_staging_24_02_26`:

| grn_id | grn_ref_no | grn_type | Should Be | Supplier | Date | Amount | User |
|---|---|---|---|---|---|---|---|
| 31 | PM-00016 | 1 (Bill) | **PU-00016** ❌ | 1 | 2026-01-20 11:34 | ₹1,44,200 | 1 |
| 32 | PM-00016 | 2 (Receipt) | PM-00016 ✅ | 23 | 2026-01-20 18:44 | ₹0.00 | 2 |

**Sequence around the duplicate:**
```
grn_id=30  PM-00015  type=2 (Receipt)  ← last entry before Jan 20
grn_id=31  PM-00016  type=1 (Bill)     ← WRONG prefix (should be PU-)
grn_id=32  PM-00016  type=2 (Receipt)  ← correct but duplicate ref_no
grn_id=33  PM-00017  type=2 (Receipt)
grn_id=34  PU-00017  type=1 (Bill)     ← CORRECT (after PUR-INT01 fix applied)
```

### Root Cause

Both `generatePurRefOrderNo()` and `generate_grn_refno()` use a **read-then-increment** (TOCTOU) pattern with **no database-level locking or UNIQUE constraint**:

1.  **Race Condition**: Multiple threads read the same `MAX()` before any one completes the `INSERT`, leading to identical numbers.
2.  **Inconsistent Business Logic (Developer Discovery)**: For "Against Order" (`stock_type == 1`), the controller previously hardcoded `is_suspense_stock = 0` during database insertion, but the reference generation code used the raw input value. If the input had `is_suspense_stock = 1` for an order, it would generate an "Approval" number (`PA-`) but assign it to a "Bill" record (`is_suspense_stock = 0`) in the database. This misaligns the prefix/sequence tracking and causes future duplicates when the `MAX()` query skips or overlaps these incorrectly typed records.

1. **Read**: SELECT MAX/last ref number from the table
2. **Increment**: Add 1 in PHP
3. **Write**: INSERT the new row with the generated number

When two users save a PO or GRN concurrently, both read the same MAX value, both increment to the same next number, and both INSERT — producing duplicate ref numbers.

**The old `generate_grn_refno()` had two critical flaws:**
1. Used `ORDER BY grn_id DESC LIMIT 1` (grabbed last row regardless of type) instead of `MAX()` with `WHERE grn_type = ?`
2. Had NO `grn_type` filter — it counted across ALL GRN types (PU, PM, PC) together, causing wrong prefix assignment

### Affected Methods

| Model | Method | Lines | Approach |
|---|---|---|---|
| `ret_purchase_order_model.php` | `generatePurRefOrderNo()` | L187-196 | `SELECT MAX(CAST(SUBSTRING_INDEX(...)))` |
| `ret_purchase_approval_model.php` | `generatePurRefOrderNo()` | L121-146 | `ORDER BY po_id DESC LIMIT 1`, extract & increment |
| `ret_purchase_order_model.php` | `generate_grn_refno()` | L2790-2816 | `ORDER BY grn_id DESC LIMIT 1`, extract & increment |
| `ret_purchase_approval_model.php` | `generate_grn_refno()` | L2079-2107 | Same as above (duplicate code) |

### Compounding Issues

1. **No UNIQUE constraint** on `ret_purchase_order.po_ref_no` or `ret_grn_entry.grn_ref_no` — the DB does not enforce uniqueness.
2. **Ref number generated OUTSIDE transaction** — `generatePurRefOrderNo()` is called at controller L1183, before `trans_begin()` at L1273. Same for GRN at L9535.
3. **Two different implementations** of `generatePurRefOrderNo()` across `ret_purchase_order_model` (MAX approach) and `ret_purchase_approval_model` (ORDER BY DESC approach) — they can produce different numbers for the same data.
4. **Duplicate code** — `generate_grn_refno()` is copy-pasted between both models.
5. **No JS double-submit prevention** — no button disable or form secret key check found for PO save.
6. **SQL injection** in `generate_grn_refno()` — `$grn_type` is concatenated directly into SQL without binding: `"WHERE grn_type=".$grn_type`.

### Partial Fix Applied

The `generate_grn_refno()` in `ret_purchase_approval_model.php` has been fixed:
- Now uses `MAX()` with `WHERE grn_type = ?` query binding
- Entries after `grn_id=34` (PU-00017, 2026-02-09 onwards) use correct prefix

### Remaining Fix Required
1. Move ref number generation INSIDE the transaction
2. Use `SELECT ... FOR UPDATE` to lock the row/table during number generation
3. Add `UNIQUE INDEX` on `(po_ref_no, fin_year_code)` and `(grn_ref_no, grn_type)`
4. Consolidate duplicate model methods into one
5. Add JS button disable on click to prevent double-submit
6. **Data fix**: Update `grn_id=31` from `PM-00016` to `PU-00016`
