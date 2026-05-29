# Flow Checklist: Estimation → Billing Conversion

> **Last Updated:** 2026-03-27
> **Source:** Estimation FLOW_RISK_MATRIX + Billing FLOW_RISK_MATRIX
> **Covers:** Estimation created → converted to bill (estimation items become bill items)

---

## Modules Involved
- **Estimation** — estimation creation (13 child tables)
- **Billing** — conversion to bill (reads estimation, creates billing records)
- **Tagging** — tag reserved in estimation, sold in billing
- **Account** — journal on billing
- **EDA** — Enhanced Discount Approval (optional workflow)

## Tables Touched (Estimation Side)
`ret_estimation`, `ret_estimation_items`, `ret_estimation_item_stones`, `ret_estimation_item_other_materials`, `ret_estimation_other_charges`, `ret_estimation_old_metal_sale_details`, `ret_esti_old_metal_stone_details`, `ret_est_chit_utilization`, `ret_est_gift_voucher_details`, `ret_est_other_metals`, `ret_est_tag_merge`, `ret_est_sales_return_utilization`, `ret_estimation_other_inventory_issue`

---

## SAVE Checklist (Estimation Create)

| # | Table | Expected | Status | Gap? |
|---|---|---|---|---|
| 1 | `ret_estimation` | INSERT header | ⬜ | — |
| 2 | `ret_estimation_items` | INSERT per item | ⬜ | — |
| 3 | All 11 child tables | INSERT per applicable data | ⬜ | — |
| 4 | Tag status check | `tag_status = 0` before adding | ❌ | **BUG**: Client-side only, server skips |
| 5 | Total calculation | Server-side recalc of total_cost | ❌ | **BUG**: Client-side JS only, no server validation |

## SAVE Checklist (Billing Conversion)

| # | Table | Expected | Status | Gap? |
|---|---|---|---|---|
| 1 | `ret_estimation.estbillid` | → {bill_id} (linked) | ✅ | — |
| 2 | `ret_estimation_items.purchase_status` | → 1 (sold) | ✅ | — |
| 3 | `is_estno_already_billed()` | Check before conversion | ✅ | — |
| 4 | All billing save tables | Same as sales_bill.md | — | — |

## CANCEL Checklist (Edit Estimation — Delete-then-Insert)

> ⚠️ Estimation has NO cancel. Edit = delete all child rows + re-insert.

| # | Table | Actually Cleaned? | Gap? |
|---|---|---|---|
| 1 | `ret_estimation_items` | ✅ YES | — |
| 2 | `ret_estimation_item_stones` | ✅ YES | — |
| 3 | `ret_estimation_other_charges` | ✅ YES | — |
| 4 | `ret_estimation_old_metal_sale_details` | ✅ YES | — |
| 5 | `ret_est_chit_utilization` | ✅ YES | — |
| 6 | `ret_est_gift_voucher_details` | ✅ YES | — |
| 7 | `ret_est_other_metals` | ⚠️ | **UNVERIFIED**: May not be cleaned |
| 8 | `ret_est_tag_merge` | ⚠️ | **UNVERIFIED**: May leave stale records |
| 9 | `ret_est_sales_return_utilization` | ⚠️ | **UNVERIFIED**: SR credits may stack |
| 10 | `ret_estimation_other_inventory_issue` | ⚠️ | **UNVERIFIED**: Packaging may duplicate |

## CANCEL Checklist (Cancel Bill with Estimation)

| # | Item | Expected | Status |
|---|---|---|---|
| 1 | `ret_estimation.estbillid` | → NULL (unlinked) | ✅ |
| 2 | `ret_estimation_items.purchase_status` | → 0 (unsold) | ✅ |

## PRINT Checklist

| # | Field | Source | Gap? |
|---|---|---|---|
| 1 | Estimation total | PHP recalculates from DB (may differ from JS save) | ⚠️ **KNOWN GAP**: JS vs PHP formula divergence |
| 2 | All item details | `ret_estimation_items` | ⬜ |

## REPORT Checklist

| # | Report | Source | Status |
|---|---|---|---|
| 1 | Estimation Report | `ret_estimation` + items | ⬜ |
| 2 | Conversion Report | `ret_estimation` where estbillid IS NOT NULL | ⬜ |

## Known Bugs Found

| Bug ID | Missing Step | Severity |
|---|---|---|
| — | Tag status not validated server-side at estimation save | 🔴 HIGH |
| — | Total calculation client-side only — no server recalc | 🔴 HIGH |
| EST-R601 | `trans_commit()` called on error branch — partial saves permanent | 🔴 CRITICAL |
| — | EDA flag can be cleared on re-edit after approval | 🟡 MED |
| — | Edit: 4 of 13 child tables not verified as cleaned | 🟡 MED |
| — | Print total may differ from saved total (JS vs PHP) | 🟡 MED |
