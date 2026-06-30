# Verification: {task_id}

> Shadow comparison and timing verification for: {summary}

## Shadow Comparison Result

| Check | Status |
|---|---|
| **`_original()` method exists** | <!-- ✅ YES / ❌ NO --> |
| **Output structure match** | <!-- ✅ IDENTICAL / ❌ DIFFERS --> |
| **Output values match** | <!-- ✅ IDENTICAL / ⚠️ WITHIN TOLERANCE / ❌ DIFFERS --> |
| **Row counts match** | <!-- ✅ YES / ❌ NO --> |

### Diff Details (if any)

<!-- If output differs, document exactly which keys differ and by how much -->
```
Key: item_details.Gold.net_wt
  Original: 45.234
  Optimized: 45.234
  Match: ✅
```

## Timing Comparison

| Metric | Before (_original) | After (optimized) | Improvement |
|---|---|---|---|
| **Execution Time** | <!-- e.g., 8.2s --> | <!-- e.g., 1.4s --> | <!-- 83% --> |
| **Query Count** | <!-- e.g., 15 --> | <!-- e.g., 15 --> | <!-- same --> |
| **Memory Usage** | <!-- e.g., 12MB --> | <!-- e.g., 10MB --> | <!-- 17% --> |

## Test Parameters Used

```
branch_id: 
counter_id: 
from_date: 
to_date: 
report_type: 
group_type: 
employee_ids: 
```

## Query Plan Verification

<!-- EXPLAIN output for the most critical query before/after -->

### Before (non-sargable)
```sql
EXPLAIN ...
-- type: ALL, rows: 50000
```

### After (sargable)
```sql
EXPLAIN ...
-- type: range, rows: 500
```

## Regression Checks

- [ ] All return array keys present in optimized version
- [ ] No NULL values where originals had data
- [ ] Number formatting consistent (decimal places match)
- [ ] Empty result set handled correctly (no errors on zero data)
- [ ] Branch filter works correctly
- [ ] Counter filter works correctly
- [ ] Floor filter works correctly
- [ ] Employee filter works correctly
- [ ] Date range filter works correctly

## Verdict

<!-- PASS / FAIL / RETEST -->
**Verdict: PENDING**

### If FAIL — Loop Back

- [ ] Document what failed
- [ ] Transition back to OPTIMIZE phase
- [ ] Fix the specific regression
- [ ] Re-run verification
