# Playbook #7: Data Mismatch Between Modules

> **Symptom**: "Dashboard count ≠ report count", "Billing total ≠ cash abstract"

## ⚡ Binary Search Integration
- **Start Layer**: 6 (SQL) — find BOTH model methods' SQL queries and diff them side by side
- **Elimination**: Same SQL → data processing differs in controllers. Different SQL → the query difference IS the mismatch (different WHERE, different date column, different bill_status filter).
- **Smell test**: "Dashboard ≠ report" → 35% different date columns (`entry_date` vs `bill_date`). "Cash abstract ≠ billing" → different data source tables (summary vs detail).

## Collect
1. Which TWO places show different data? (exact module + screen)
2. What is the value in location A?
3. What is the value in location B?
4. For what period/filter? (date range, branch, bill type)

## Trace
1. Find BOTH query sources:
   - MODULE_BRAIN for Module A → find the model method → extract SQL
   - MODULE_BRAIN for Module B → find the model method → extract SQL
2. Compare the two SQL queries side by side:
   - Same tables? If different → different data sources
   - Same WHERE conditions? If different → different filter logic
   - Same GROUP BY? If different → different aggregation
   - Same date column? (`entry_date` vs `created_at` vs `trans_date`)
   - Same bill_status filter? (one may exclude cancelled, other doesn't)
3. Run both queries with identical parameters and compare:
   ```sql
   -- Module A's query
   SELECT ... WHERE date BETWEEN '{start}' AND '{end}' AND branch = {X};
   -- Module B's query
   SELECT ... WHERE date BETWEEN '{start}' AND '{end}' AND branch = {X};
   ```

## Common Root Causes
1. **35%**: Different date columns (`entry_date` vs `bill_date` vs `created_at`)
2. **25%**: One includes cancelled bills, other excludes them
3. **20%**: Different table sources (one uses summary table, other queries detail)
4. **10%**: Round-off differences (SUM on decimal vs stored total)
5. **10%**: Time zone / day_close boundary mismatch

## Product-Specific Traps
- Dashboard often uses cached/summary tables; reports query live data
- Cash abstract uses `ret_billing` totals; day transactions sums `ret_bill_pay_device`
- Chit reports use `payment` table; billing uses `ret_billing` — different amounts
