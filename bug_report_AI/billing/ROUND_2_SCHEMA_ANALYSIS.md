# Billing Module Bug Audit — Round 2: DB Schema Cross-Reference

> **Module**: Billing | **Date**: 2026-02-24 | **Auditor**: Antigravity
> **Source**: `SCHEMA_ANALYSIS.md` + Live DB `DESCRIBE` (verified 2026-02-24)

---

## Summary

| Severity      | Count |
| ------------- | ----- |
| P0 (Critical) | 1     |
| P1 (High)     | 1     |
| P2 (Medium)   | 2     |
| **Total**     | **4** |

---

## Bugs Found

### BIL-S01 — No Database-Level Foreign Key Constraints (P2)

**Location**: All 10 billing tables
**Description**: The schema uses FK columns (`bill_cus_id → customer`, `bill_id → ret_billing`, etc.) but these are app-level assumptions only — no actual `FOREIGN KEY` constraints exist in the DB. This means:

- Deleting a customer doesn't cascade to their bills
- Orphan records in child tables if header is deleted
- No referential integrity enforcement
  **Impact**: Data integrity relies entirely on application code (which has bugs — see BIL-001).
  **Track**: A — Schema

---

### BIL-S02 — `bill_type` Uses Magic Numbers Without Enum/Check Constraint (P2)

**Location**: `ret_billing.bill_type` (`tinyint`, not `ENUM`)
**Description**: `bill_type` stores values 1–15 with specific meanings (1=Normal, 2=Exchange, 3=Combined, 4=Purchase, 5=OrderAdv, 7=Return, 8=CreditCollect, 9/15=variants). No CHECK constraint or ENUM limits valid values. Nothing prevents inserting `bill_type=99` into the DB.
**Impact**: Invalid type values could break all bill-type-dependent logic, reports, and UI rendering.
**Track**: A — Schema

---

### BIL-S03 — Financial Columns Use `decimal(10,0)` — Zero Decimal Places (P0) ✅ CONFIRMED

**Location**: Multiple columns in `ret_billing` and `ret_bill_details`
**Verified via**: Live `DESCRIBE ret_billing` and `DESCRIBE ret_bill_details` on `retaillogimaxind_etailv3`

**`ret_billing` — 5 columns with `decimal(10,0)`:**

| Column             | Type            | Should Be       | Impact                                                  |
| ------------------ | --------------- | --------------- | ------------------------------------------------------- |
| `tot_purchase_amt` | `decimal(10,0)` | `decimal(12,2)` | Purchase amount truncated — ₹1234.56 → ₹1234            |
| `tot_amt_received` | `decimal(10,0)` | `decimal(12,2)` | Amount received truncated — payment mismatches          |
| `tot_bill_amount`  | `decimal(10,0)` | `decimal(12,2)` | **TOTAL BILL AMOUNT** truncated — invoice discrepancies |
| `tot_sale_amt`     | `decimal(10,0)` | `decimal(12,2)` | Sale amount truncated — report totals wrong             |
| `advance_deposit`  | `decimal(10,0)` | `decimal(12,2)` | Advance deposit truncated — customer balance errors     |

**`ret_bill_details` — 2 columns with `decimal(10,0)`:**

| Column                  | Type            | Should Be       | Impact                     |
| ----------------------- | --------------- | --------------- | -------------------------- |
| `sales_return_discount` | `decimal(10,0)` | `decimal(10,2)` | Return discount paise lost |
| `return_item_cost`      | `decimal(10,0)` | `decimal(10,2)` | Return cost paise lost     |

**Good news** — percentage columns are correctly typed:

- `tcs_tax_per` → `decimal(10,3)` ✅
- `tds_percent` → `decimal(10,2)` ✅
- `wastage_percent` → `decimal(10,2)` ✅

**Impact**: Every bill's total amount, purchase amount, sale amount, and advance deposit silently loses fractional paise during INSERT. Over thousands of bills, cumulative rounding loss becomes significant. Tax authorities may flag discrepancies between item-level totals (which use `decimal(10,2)`) and bill-level totals (which use `decimal(10,0)`).
**Track**: B — Financial data loss (requires business decision on migration approach)

---

### BIL-S04 — `round_off_amt` Stored as VARCHAR(40) Instead of Decimal (P1) ✅ CONFIRMED

**Location**: `ret_billing.round_off_amt` — `varchar(40)` with default `'0'`
**Verified via**: Live `DESCRIBE ret_billing`

**Description**: A financial amount column is stored as a **string**. This means:

- No numeric constraints — could store `"abc"` or `"-"` as a round-off amount
- Sorting is alphabetical, not numeric (`"9"` > `"10"`)
- SUM/AVG queries require CAST, which silently ignores non-numeric values
- Adding `round_off_amt` to other `decimal` columns requires implicit conversion

Also note: the corresponding column in `ret_bill_details` is `round_of_amt` (typo: `of` vs `off`) and uses `decimal(10,0)` — both are problematic.

**Impact**: Financial aggregation queries may produce wrong results. Non-numeric values would silently be treated as `0` in SUM queries.
**Track**: A — Schema (varchar for numeric data)

---

## Round 2 Result

```
Round 2: Schema Analysis Complete
├── Total bugs: 4
├── P0 (Critical): 1 — BIL-S03 (7 decimal(10,0) financial columns — CONFIRMED)
├── P1 (High): 1 — BIL-S04 (round_off_amt as varchar — CONFIRMED)
├── P2 (Medium): 2 — BIL-S01 (no FK constraints), BIL-S02 (magic numbers without ENUM)
├── Track A (System): 3
└── Track B (Business): 1
```
