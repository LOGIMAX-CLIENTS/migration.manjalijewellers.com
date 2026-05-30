# Playbook #10: Performance Issue

> **Symptom**: "Page takes 30 seconds to load", "Report is very slow"

## ⚡ Binary Search Integration
- **Start Layer**: 6 (SQL) — `$this->output->enable_profiler(TRUE)` in the controller, or `EXPLAIN {query}`
- **Elimination**: Profiler shows slow query → add index or fix N+1. Profiler shows many queries → N+1 loop. Profiler shows few fast queries → the slowness is in PHP logic (large array processing, JSON encoding).
- **Smell test**: "Slow report" → 40% N+1 query in foreach loop. "Slow page load" → 30% missing DB index on WHERE column.

## Collect
1. Which page/report?
2. Approximate load time? (seconds)
3. Is it slow for specific data (lots of records) or always?
4. When did it start being slow?

## Trace

### Step 1: Identify the slow query
- Find the model method (MODULE_BRAIN → METHOD_INDEX)
- Read the SQL → check for:
  - Missing `LIMIT` (returns all rows)
  - Missing WHERE index (full table scan)
  - N+1 pattern: query inside a `foreach` loop
  - Subqueries that could be JOINs
  - `COUNT(*)` without WHERE on large tables

### Step 2: Check for N+1 pattern
```php
// BAD — query inside loop (N+1):
$bills = $this->db->get('ret_billing')->result_array();
foreach ($bills as $bill) {
    $details = $this->db->get_where('ret_bill_detail', 
        array('id_billing' => $bill['id_billing']))->result_array();
}

// GOOD — single join query:
$this->db->select('b.*, d.*');
$this->db->join('ret_bill_detail d', 'd.id_billing = b.id_billing');
$this->db->get('ret_billing b');
```

### Step 3: Check indexes
```sql
SHOW INDEX FROM {slow_table};
EXPLAIN SELECT ... {the slow query};
```
Look for `type: ALL` in EXPLAIN → missing index.

## Common Root Causes
1. **40%**: N+1 query pattern (loop-inside-loop)
2. **30%**: Missing database index on frequently filtered columns
3. **15%**: Unbounded query (no WHERE/LIMIT on large table)
4. **10%**: Large result set serialized as JSON (PHP memory + encoding time)
5. **5%**: External API call (SMS gateway, rate API) blocking synchronously
