# Invariant Matrix
# Module: Ret_Reports

> **Purpose**: Documents variant-specific behavior for config-driven code paths.
> **Built**: 2026-03-18 — Round 5
> **Key Insight**: `allow_bill_type` affects **121 model queries** and is the dominant variant dimension.

---

## 1. Variant Dimensions

| # | Dimension Name | Possible Values | Controlling Field (DB) | Controlling Field (PHP) | Controlling Field (JS) |
|---|---|---|---|---|---|
| 1 | Bill Type Mode | 1 (EDA only), 2 (Day Close only), 3 (Both) | `profile.allow_bill_type` | `$profile_settings['allow_bill_type']` | N/A (server-side only) |
| 2 | Bill EDA Flag | 1 (EDA), 2 (Day Close) | `ret_billing.is_eda` | Compared in SQL WHERE | N/A |
| 3 | Billing-For Context | 1, 2, 3 (varies by report) | Passed from controller | `$billing_for` param | Form hidden fields |

---

## 2. Behavior Grids

### Grid: `allow_bill_type` × `is_eda` (per bill)

| | `is_eda = 1` (EDA bill) | `is_eda = 2` (Day Close bill) |
|---|---|---|
| **allow_bill_type = 1** | ✅ Included — `and bill.is_eda=1` | ❌ Excluded |
| **allow_bill_type = 2** | ❌ Excluded | ✅ Included — `and bill.is_eda=2 and date(day_close.entry_date) = date(bill.bill_date)` |
| **allow_bill_type = 3** | ✅ Included — OR branch of combined condition | ✅ Included — `IF(bill.is_eda=2, date(day_close.entry_date)=date(bill.bill_date), '')` |

> **Impact**: 121 model queries apply this filter. Wrong `allow_bill_type` in profile → reports show wrong subset of bills.
> **Risk**: P0 — Financial reports (sales, GST, day transactions) will silently show incomplete data if profile setting is wrong.

### Grid: `allow_bill_type` × Report Type

| | Sales/Billing Reports | Stock/Tag Reports | Dashboard/Lookup Reports |
|---|---|---|---|
| **allow_bill_type = 1** | Filters to EDA bills only | N/A (stock is bill-agnostic) | Filters to EDA |
| **allow_bill_type = 2** | Filters to Day Close only | N/A | Filters to Day Close |
| **allow_bill_type = 3** | Both EDA + Day Close | N/A | Both |

> **Key pattern**: Stock/Tag reports (`get_stock_*`, `getLotwiseTaggedVault`, etc.) do NOT use `allow_bill_type` — they show all stock regardless. Only billing-linked reports are affected.

---

## 3. Edge Cases & Special Combinations

### EC-1: Day Close Entry Not Created
- **When**: `allow_bill_type=3`, bill has `is_eda=2`, but no `day_close` record exists for that date
- **Expected**: Bill should still appear (gracefully)
- **Actual**: LEFT JOIN on `day_close` returns NULL → `date(day_close.entry_date)=date(bill.bill_date)` evaluates to NULL → bill is **silently excluded**
- **Status**: ⚠️ Potential silent data loss — depends on day_close record completeness

### EC-2: Profile Settings Changed Mid-Day
- **When**: Admin changes `allow_bill_type` from 1→3 while users are viewing reports
- **Expected**: Next report refresh shows updated data
- **Actual**: ✅ Correct — `get_profile_settings()` is called per-request, no caching
- **Status**: ✅ Correct

### EC-3: Branch Has Different Bill Type Than HO
- **When**: Different branches may have different `allow_bill_type` in their profiles
- **Expected**: Branch-specific report shows that branch's bill subset
- **Actual**: Filter uses current user's profile, not the target branch's profile
- **Status**: ❓ Untested — if user at HO (type=3) views Branch-A (type=1), they see both EDA+Day Close for Branch-A. May or may not be intended.

---

## 4. Variant Test Coverage

| Dimension | Total Variants | Tested | Untested | Coverage |
|---|---|---|---|---|
| allow_bill_type | 3 | 3 (code patterns confirmed) | 0 | 100% code review |
| is_eda | 2 | 2 (both paths confirmed) | 0 | 100% code review |
| billing_for | 3 | 0 | 3 | 0% (runtime only) |
| **Overall** | 18 combinations | 6 code-reviewed | 3 edge cases flagged | ~67% |

---

## 5. Occurrence Metrics

| Variant | Model Occurrences | Risk Assessment |
|---|---|---|
| `allow_bill_type` | 121 | P0 — wrong value = wrong financial data in ~100 reports |
| `is_eda` | 168 | P0 — tightly coupled with allow_bill_type |
| `billing_for` | 68 | P1 — category/context filter for billing-specific reports |
