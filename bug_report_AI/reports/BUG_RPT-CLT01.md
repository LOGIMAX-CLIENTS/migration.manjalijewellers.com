## RPT-CLT01 — Weight Range Not Showing in Re-Order Setting Report

| Field | Value |
|---|---|
| Severity | P1 |
| Track | A (System) |
| Category | Logic |
| Sprint | Sprint 1 |
| Pattern Match | None |
| Module Brain | ✅ Ready (purchase) |
| Reporter | Client |
| Source | Client |

### Steps to Reproduce
1. Navigate to **Reports > Re-order Items** (`admin_ret_reports/reorder_items/list`)
2. Select a Branch (e.g., ARC SUNDHARAM JEWELLERS)
3. Select a Product (e.g., RING)
4. Click **Search**
5. Observe the "Weight Range" column in the report table

### Expected Behavior
The "Weight Range" column should display the configured weight range values (e.g., "0-5 Grams", "5-10 Grams", or at least the from/to weight values like "0.000 - 5.000") for each row.

### Actual Behavior
The "Weight Range" column is **blank/empty** for all or most rows, even though the rows clearly have weight range data (as evidenced by `from_weight`/`to_weight` being populated in the `ret_reorder_settings` table and the HAVING clause filtering for non-null values).

### Root Cause Analysis
In `ret_reports_model.php` line 3023, the `weight_name` is built as:
```sql
CONCAT(IFNULL(wt.weight_description, ''), ' ', IFNULL(m.uom_name, '')) AS weight_name
```
This relies on `wt.weight_description` from `ret_weight` table to be populated. When `weight_description` is NULL or empty, the result is just a space character `' '`, which renders as blank.

**Fix**: Use `CONCAT(wt.from_weight, ' - ', wt.to_weight)` as a fallback when `weight_description` is empty.

### Evidence
Screenshot attached showing blank Weight Range column in the report.
