## TAG-CLT01 — NaN in Footer Total Row

| Field | Value |
|---|---|
| Severity | P0 — Critical |
| Track | B (Business Logic) |
| Category | 1 (Logic) |
| Sprint | Sprint 1 |
| Pattern Match | PAT-BIZ-001 (JS/PHP calc mismatch) / TAG-R501 (Unguarded parseFloat) |
| Module Brain | ✅ Ready |
| Reporter | Developer |
| Source | Client/User Report |

### Steps to Reproduce
1. Navigate to `http://localhost/etail/admin/index.php/admin_ret_tagging/retagging/add`
2. Observer the "Total" row calculation.
3. Observe one or more columns displaying `NaN`.

### Expected Behavior
The footer should correctly sum all values from the above rows (Weight, Pieces, Value, etc.) and display them as numbers with proper formatting.

### Actual Behavior
The table footer displays `NaN` for certain total values, likely due to an empty or non-numeric field being passed to `parseFloat()` without a fallback `|| 0`.

### Evidence
Reported via user message with screenshot showing `NaN` in the third-to-last column of the footer total row.

### Fix Details
1. **NaN Prevention**: Corrected `ret_tagging.js` by replacing unstable `isNaN()` guards with robust `parseFloat(val) || 0`. This fix was applied to the following functions:
   - `calculate_SalesReturn_RowTotal()`
   - `calculate_PartlySale_RowTotal()`
   - `calculate_OldMetal_RowTotal()`
   - `calculateNT_OtherIssue_total()`
   - `calculate_average_purity_and_rate()`
2. **Alignment Mismatch Correction**: In `retagging_form.php`, added missing `<td>` elements in the footers of `retagging_list` and `partly_sale_list` to align calculation values with their respective columns (`Pcs`, `Gross Wt`, etc.).
3. **Division-by-Zero Protection**: Added `total_pcs > 0` checks when calculating `avg_purity_per` to avoid displaying `Infinity`.

**Status**: ✅ Fixed in Sprint 1.
