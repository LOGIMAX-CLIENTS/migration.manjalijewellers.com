# Playbook #1: Page Shows Wrong Data

> **Symptom**: "The total is wrong", "Wrong customer name", "Amount doesn't match"

## ⚡ Binary Search Integration
- **Start Layer**: 3 (AJAX Response) — check if the response JSON has the wrong value
- **Elimination**: Response correct → bug is in JS rendering (DataTable config, footer callback). Response wrong → go to Layer 6 (SQL).
- **Smell test**: "Total wrong on report" → 80% chance it's DataTable footer column index (PAT-DT-001). "JS total ≠ PHP total" → 25% chance JS uses different math/rounding.

## Collect (Ask Human)
1. Which page? (exact URL or menu path)
2. Which field is wrong?
3. What is the WRONG value?
4. What is the EXPECTED value?
5. Specific record or all records? (give bill number / ID)

## Trace (AI Does)

### Step 1: Find the data source
```
MODULE_BRAIN → controller method for this page
METHOD_INDEX → model method that returns this field
Read model method → extract SQL query
```

### Step 2: Check the SQL query for
- Missing WHERE clause (shows all instead of filtered)
- Wrong JOIN (LEFT vs INNER → phantom or missing rows)
- Missing GROUP BY (overcounting)
- Wrong column referenced (column name typo or wrong alias)
- Hardcoded filter values
- Missing `bill_status != 2` (cancelled bills included)
- Missing `tag_status` filter (in-transit tags counted)

### Step 3: Generate investigation queries
```sql
-- Get raw record data
SELECT * FROM {table} WHERE {id_field} = {id_value};

-- Compare with what the report/page query returns
{paste the model's SQL query here with the specific ID}

-- Check if other modules wrote to this record
SELECT * FROM log_detail WHERE record = '{id_value}' ORDER BY id_log_detail DESC LIMIT 20;
```

## Common Root Causes (probability order)
1. **40%**: SQL query bug — wrong JOIN, missing WHERE, wrong column
2. **25%**: JS calculation ≠ PHP calculation (frontend shows different math)
3. **15%**: Cross-module corruption — another module wrote wrong data
4. **10%**: Stale data — rate at page load vs rate at save time
5. **10%**: Schema — decimal truncation, type mismatch, NULL vs 0

## Output
→ Exact file, function, line number, what the query does wrong
→ Hand off to Developer role for fix
