# Hypothesis: {task_id}

> Optimization plan for: {summary}

## Identified Bottlenecks

<!-- List every bottleneck found during PROFILE phase, ranked by impact -->

### 1. <!-- e.g., Non-sargable date filters -->
- **Impact**: <!-- HIGH/MEDIUM/LOW -->
- **Location**: <!-- Line range in the method -->
- **Problem**: <!-- e.g., date(col) BETWEEN prevents index usage -->
- **Fix**: <!-- e.g., col BETWEEN 'date' AND 'date 23:59:59' -->
- **Expected Gain**: <!-- e.g., 3.2s → 0.4s -->

### 2. <!-- e.g., N+1 query pattern -->
- **Impact**: <!-- HIGH/MEDIUM/LOW -->
- **Location**: <!-- Line range -->
- **Problem**: <!-- e.g., Loop calls getOldMetalPurchaseAmount() per row -->
- **Fix**: <!-- e.g., Batch LEFT JOIN subquery -->
- **Expected Gain**: <!-- e.g., 2.1s → 0.1s -->

### 3. <!-- e.g., Duplicated WHERE clauses -->
- **Impact**: <!-- LOW — maintainability, not speed -->
- **Location**: <!-- Entire method -->
- **Problem**: <!-- e.g., Same 6-line WHERE copied 15 times -->
- **Fix**: <!-- e.g., DRY $bill_where / $receipt_where_base variables -->
- **Expected Gain**: <!-- Minimal runtime gain, but prevents future copy-paste bugs -->

## Optimization Strategy

<!-- Order of operations — which fix to apply first and why -->

1. <!-- Apply sargable date fix first (highest impact, lowest risk) -->
2. <!-- Apply N+1 batch fix (high impact, medium risk — changes JOIN structure) -->
3. <!-- Apply DRY WHERE refactor (low risk, code quality) -->

## Risk Assessment

| Risk | Likelihood | Mitigation |
|---|---|---|
| Output format changes | <!-- LOW --> | Shadow compare gate — blocks if output differs |
| Missing rows in JOIN | <!-- MEDIUM --> | Use LEFT JOIN (not INNER), verify row counts |
| Number formatting difference | <!-- LOW --> | Compare with toFixed(2)/number_format(3) tolerance |

## Index Requirements

<!-- Any new database indexes needed? -->
```sql
-- Required indexes (add to migration file):
```

## Shadow Compare Plan

- **Method**: `{method}()` vs `{method}_original()`
- **Test Parameters**: <!-- e.g., branch_id=1, from_date=2026-01-01, to_date=2026-01-31 -->
- **Expected Result**: Identical output structure and values
