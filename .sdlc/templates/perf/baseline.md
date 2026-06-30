# Baseline: {task_id}

| Field | Value |
|---|---|
| **Task ID** | {task_id} |
| **Type** | perf |
| **Module** | {module} |
| **Priority** | {priority} |
| **Created** | {created_at} |

## Target Method

- **Model**: <!-- e.g., ret_reports_model -->
- **Method**: <!-- e.g., getBillDetails() -->
- **File**: <!-- e.g., admin/application/models/ret_reports_model.php:575 -->
- **Line Count**: <!-- e.g., 673 lines -->

## Current Performance (Baseline)

| Metric | Value |
|---|---|
| **Avg Response Time** | <!-- e.g., 8.2s --> |
| **P95 Response Time** | <!-- e.g., 12.4s --> |
| **Query Count** | <!-- e.g., 15 queries per call --> |
| **Slowest Query** | <!-- e.g., 3.2s — items_query with date() wrapper --> |
| **Affected Page(s)** | <!-- e.g., Cash Abstract Report --> |
| **Users Impacted** | <!-- e.g., All branch managers, ~20 users --> |

## APM Evidence

<!-- Paste APM dashboard data, slow query log entries, or EXPLAIN output -->
```sql
-- Slowest query from APM:
```

## Target Performance

| Metric | Current | Target | Improvement |
|---|---|---|---|
| Avg Response Time | <!-- 8.2s --> | <!-- <2s --> | <!-- 75%+ --> |
| Query Count | <!-- 15 --> | <!-- 15 (same, but faster) --> | <!-- N/A --> |

## Acceptance Criteria

- [ ] Response time reduced to target (measured via shadow compare)
- [ ] Shadow comparison: output matches _original() exactly
- [ ] No data regression: all return keys present with correct values
- [ ] No N+1 queries remaining in the optimized method

## Known Risks

<!-- What could break? Cross-module dependencies? -->
- [ ] Method is called from: <!-- list controllers -->
- [ ] Return format changes: <!-- YES/NO — if yes, list downstream impacts -->
