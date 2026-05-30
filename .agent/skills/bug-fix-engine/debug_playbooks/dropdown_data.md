# Playbook #5: Wrong Dropdown Data

> **Symptom**: "Design dropdown shows wrong designs", "Customer list is empty"

## ⚡ Binary Search Integration
- **Start Layer**: 3 (AJAX Response) — F12 → Network → find the dropdown AJAX call → check response JSON
- **Elimination**: Response has correct data → JS is binding wrong (check `data-id`, `<option>` rendering). Response has wrong data → check the model SQL WHERE clause (branch filter, status filter).
- **Smell test**: "Empty dropdown" → 30% source table is empty, 45% wrong WHERE filter. "Wrong items" → wrong branch or category filter in SQL.

## Collect
1. Which dropdown? (field label)
2. What's shown vs expected?
3. Is it empty or showing wrong items?

## Trace
1. Find the AJAX endpoint that populates the dropdown (view JS → `$.ajax` → URL)
2. Read the controller method → find the model method
3. Read the SQL query — check WHERE conditions, especially:
   - Branch filter (wrong `id_branch` or missing)
   - Status filter (showing inactive items)
   - Category/type filter (wrong `id_category`)
4. Check if the dropdown source table has data at all:
   ```sql
   SELECT COUNT(*) FROM {table} WHERE status = 1;
   ```

## Common Root Causes
1. **45%**: Wrong WHERE filter (branch ID, status, category)
2. **30%**: Empty source table (data not migrated or truncated)
3. **15%**: JS binds wrong `data-id` attribute → server gets wrong parameter
4. **10%**: Cascading dropdown (parent selection not passing to child AJAX call)
