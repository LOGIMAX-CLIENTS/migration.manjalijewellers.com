# Recipe: Branch Transfer Summary — Grand Total Mismatch (Group By Section/Product)

## Metadata
- **Pattern ID**: RPT-BT-001
- **Severity**: P1 — Wrong output, customer-facing (report totals incorrect)
- **Modules Affected**: Retail Reports → Branch Transfer Report
- **Auto-fixable**: Yes (2-line SQL change)

## Client Scope
- **Applies to**: ALL clients using the Branch Transfer Report with Summary mode
- **Reason**: Universal — the GROUP BY bug exists in the shared `getBranchTransReport()` method

## Created By
- **Developer**: Antigravity AI
- **Client**: erp.manepally.com
- **Date**: 2026-05-18
- **Source Bug ID**: N/A (reported via screenshot comparison)

---

## Symptom

In the **Branch Transfer Report** (Summary mode), Grand Totals for **Gross Wt**, **Net Wt**, and **Dia Wt** are **different** when `Group By = Section` or `Group By = Product` compared to `Group By = Branch Transfer` or the Detailed view.

Example mismatch observed:
| Group By | Gross Wt | Net Wt | Dia Wt |
|---|---|---|---|
| Branch Transfer | 2,914.773 | 2,350.404 | 20.935 |
| **Section (broken)** | **2,914.798** | **2,350.429** | **20.910** |
| Product | 2,914.773 | 2,350.404 | 20.935 |

Difference is ±0.025g across multiple columns — not a rounding error.

---

## Root Cause

In `getBranchTransReport()` (branchtranstype == 1 and == 5), the outer SELECT includes `pro.stone_type` as a **non-aggregated column** in a `GROUP BY tag.id_section` or `GROUP BY tag.product_id` context.

The JavaScript rendering function uses `stone_type` to decide column routing:
```javascript
grs_wt += (item.stone_type != 2 ? parseFloat(item.grs_wt) : 0);       // metal → Gross Wt
dia_wt += (item.stone_type == 2 ? parseFloat(item.grs_wt) : parseFloat(item.dia_wt)); // solitaire → Dia Wt
```

When a **section contains both metal tags** (`stone_type=1`) **and solitaire/loose diamond tags** (`stone_type=2`), MySQL's non-deterministic GROUP BY picks **one arbitrary `stone_type`** for the collapsed group. If it picks `2` for a mostly-metal section, `grs_wt` gets routed into the Dia column, shifting weight values incorrectly.

This does NOT affect `Group By = Branch Transfer` because each transfer is homogeneous enough, or by coincidence stone_type is consistent per transfer.

**Secondary issue also fixed:** The diamond (`dia`) and other-stone (`stn`) subqueries were missing `b.transfer_item_type=1` in their WHERE clause, meaning stones from tags appearing in other transfer types were being included.

---

## Detection

```powershell
# Find the GROUP BY clause in getBranchTransReport
grep -n "GROUP BY tag.id_section" admin/application/models/ret_reports_model.php
```

If the result is `GROUP BY tag.id_section` (without `, pro.stone_type`), the bug is present.

```powershell
# Also check the stone subqueries for missing transfer_item_type filter
grep -n "WHERE st.stone_type=1 AND b.status!=3" admin/application/models/ret_reports_model.php
```

If found without `AND b.transfer_item_type=1`, the secondary issue is also present.

---

## Files

- `admin/application/models/ret_reports_model.php`
  - Function: `getBranchTransReport()`
  - Approximate lines: `$sql` query GROUP BY (~line 1887) and `$sql5` query GROUP BY (~line 1971)
  - Approximate lines: `dia` subquery WHERE (~line 1850) and `stn` subquery WHERE (~line 1867)

---

## Fix

### Fix 1: Add `pro.stone_type` to GROUP BY (PRIMARY FIX — both `$sql` and `$sql5` queries)

#### Before
```php
".($data['report_type']==1 ? " GROUP BY tag.tag_id " : ($data['group_by']==1 ? " GROUP BY b.branch_transfer_id " : ($data['group_by']==2 ? " GROUP BY tag.id_section " : " GROUP BY tag.product_id")))."
```

#### After
```php
".($data['report_type']==1 ? " GROUP BY tag.tag_id " : ($data['group_by']==1 ? " GROUP BY b.branch_transfer_id " : ($data['group_by']==2 ? " GROUP BY tag.id_section, pro.stone_type " : " GROUP BY tag.product_id, pro.stone_type")))."
```

> Apply this change **twice** — once for `$sql` (transfer_item_type=1) and once for `$sql5` (transfer_item_type=5).

---

### Fix 2: Add `b.transfer_item_type=1` to stone subqueries (SECONDARY FIX)

#### Before — diamond subquery
```php
WHERE st.stone_type=1 AND b.status!=3
```

#### After — diamond subquery
```php
WHERE st.stone_type=1 AND b.transfer_item_type=1 AND b.status!=3
```

#### Before — other stone subquery
```php
WHERE st.stone_type!=1 AND b.status!=3
```

#### After — other stone subquery
```php
WHERE st.stone_type!=1 AND b.transfer_item_type=1 AND b.status!=3
```

---

## Verification

1. Open Branch Transfer Report → Summary mode
2. Run the same date range with:
   - Group By = Branch Transfer → note Grand Total Gross Wt
   - Group By = Section → Grand Total Gross Wt must **match exactly**
   - Group By = Product → Grand Total Gross Wt must **match exactly**
3. All three Grand Total rows (Pieces, Gross Wt, Net Wt, Stn Wt, Dia Wt) must be identical across groupings

---

## Notes

- The JS routing logic (`stone_type==2 → use grs_wt as dia_wt`) is intentional for solitaire/loose diamond items that have no separate stone weight entry — their gross weight IS their diamond weight. The fix ensures MySQL doesn't pick a wrong stone_type for collapsed groups.
- This pattern applies to any report where `stone_type` is selected as a non-aggregated column alongside a GROUP BY that collapses mixed-type rows.
- The secondary fix (transfer_item_type filter on subqueries) prevents stone weight inflation for tags that appear in multiple transfer types.
