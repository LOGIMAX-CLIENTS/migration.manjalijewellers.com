# Playbook #8: Duplicate Records

> **Symptom**: "Same receipt number twice", "Duplicate bill", "Customer appears twice"

## ⚡ Binary Search Integration
- **Start Layer**: 7 (DB) — confirm duplicates exist with GROUP BY + HAVING COUNT > 1
- **Elimination**: Duplicates at same timestamp → double-click / race condition (fix in JS + add UNIQUE). Duplicates at different times → no uniqueness constraint + logic allows re-creation (fix in DB + model).
- **Smell test**: "Same second timestamp" → 40% double-click submit. "Same value, different days" → 15% no UNIQUE constraint. "After import" → 10% import script ran twice.

## Collect
1. Which table has duplicates? (customer, billing, payment, etc.)
2. What field is duplicated? (receipt_no, acc_no, mobile)
3. How many duplicates? (2 records or many?)
4. When did it happen? (specific date or ongoing)

## Trace
1. Confirm duplicates exist:
   ```sql
   SELECT {duplicate_field}, COUNT(*) as cnt 
   FROM {table} 
   GROUP BY {duplicate_field} 
   HAVING cnt > 1
   ORDER BY cnt DESC LIMIT 20;
   ```
2. Check the save logic for race conditions:
   - Is there a `SELECT MAX()+1` for auto-numbering? → Race condition under concurrent access
   - Is there a `UNIQUE` constraint on the field? If NO → no DB-level protection
   - Is there a `LOCK TABLES` or `FOR UPDATE`? If NO → concurrent writes possible
3. Check if duplicates were created at the same timestamp:
   ```sql
   SELECT * FROM {table} 
   WHERE {duplicate_field} = '{value}' 
   ORDER BY created_at;
   ```
   Same second → concurrent request / double-click

## Common Root Causes
1. **40%**: Double-click on submit (no JS debounce + no server-side dedup)
2. **30%**: `SELECT MAX()+1` race condition (two requests get same number)
3. **15%**: No UNIQUE constraint → DB allows duplicates silently
4. **10%**: Import script ran twice
5. **5%**: Browser back button + re-submit

## Fix Patterns
- Add `UNIQUE INDEX` to the column
- Add JS submit debounce: disable button after first click
- Use `INSERT ... ON DUPLICATE KEY UPDATE` instead of blind INSERT
- For auto-numbers: use DB `AUTO_INCREMENT` or `SELECT ... FOR UPDATE`
