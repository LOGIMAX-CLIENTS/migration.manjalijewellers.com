# Forensic Investigation Template
# Module: {MODULE_NAME}

> **Purpose**: Layer-by-layer investigation cheat sheet for diagnosing bugs in this module.
> **When to build**: During `/build-module-brain` Step 6c — build for every module.
> **How to use**: When a bug is reported, start at Layer 1 and work down until root cause is found.
> **Example**: See `knowledge_brain/Payment/FORENSIC_TEMPLATE.md` for a completed example.

---

## Layer 1: Symptom Collection

### Common Symptoms in {MODULE_NAME}
| # | Symptom | Likely Layer | Severity |
|---|---|---|---|
| 1 | {e.g., "Wrong total displayed"} | {JS / Controller / Model / DB} | {P0-P3} |
| 2 | {e.g., "Record not saving"} | {Controller / Model} | {P0-P3} |
| 3 | {e.g., "Duplicate entries created"} | {Model / DB} | {P0-P3} |
| ... | ... | ... | ... |

### Data to Collect Immediately
- [ ] Screenshot / exact error message
- [ ] URL and route that produced the error
- [ ] Input data that triggered the issue
- [ ] Browser console errors (F12 → Console)
- [ ] Network tab response (F12 → Network → XHR)
- [ ] User's branch/configuration
- [ ] Record ID(s) affected

---

## Layer 2: Reproduce & Isolate

### Reproduction Checklist
1. [ ] Can you reproduce with the SAME input data?
2. [ ] Can you reproduce with DIFFERENT input data?
3. [ ] Does it happen for ALL users or specific configuration?
4. [ ] Does it happen in ALL branches or specific branch?
5. [ ] Check INVARIANT_MATRIX: Is this variant-specific?

### Isolation Questions
- **When did it start?** → Check recent commits
- **Who reported it?** → Check their configuration/branch
- **Intermittent or consistent?** → If intermittent, likely concurrency/timing
- **Data-dependent?** → Check edge case values (0, NULL, negative, very large)

---

## Layer 3: Client-Side Trace (JavaScript)

### JS File: `{module_js_file}`

### Key Console Log Points
```javascript
// Add these temporarily to trace the issue:

// 1. On form submit / save button click
console.log('{MODULE} — Submit triggered', {
    field1: $('#field1').val(),
    field2: $('#field2').val(),
    // ... list all relevant field selectors
});

// 2. Before AJAX call
console.log('{MODULE} — AJAX payload:', formData);

// 3. On AJAX success
console.log('{MODULE} — Server response:', response);

// 4. On calculation function
console.log('{MODULE} — Calc input:', { a, b, c });
console.log('{MODULE} — Calc result:', result);
```

### Key Variables to Inspect
| Variable | Where | Expected | How to Check |
|---|---|---|---|
| `{variable_name}` | `{function_name}()` line {N} | `{expected_value}` | Console → `$('#selector').val()` |
| ... | ... | ... | ... |

### Network Tab Checks
| Endpoint | Method | Expected Status | Key Params |
|---|---|---|---|
| `{controller/method}` | POST | 200 | `{param1, param2}` |
| ... | ... | ... | ... |

---

## Layer 4: Server-Side Trace (PHP)

### Controller: `{Controller_name}.php`

### Trace Points Table
| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| {symptom 1} | `{file}.php` | `{method}()` | {line} | {what to inspect} |
| {symptom 2} | `{file}.php` | `{method}()` | {line} | {what to inspect} |
| ... | ... | ... | ... | ... |

### Model: `{Model_name}.php`

### Query Investigation Points
| Method | Query Type | Table(s) | Common Issue |
|---|---|---|---|
| `{method}()` | SELECT/INSERT/UPDATE | `{table}` | {e.g., missing WHERE clause} |
| ... | ... | ... | ... |

---

## Layer 5: Database Verification

### Diagnostic SQL Queries

#### 5a. Pull Complete Transaction
```sql
-- Get header + all child records for a specific transaction
SELECT h.*, d.*
FROM {header_table} h
LEFT JOIN {detail_table} d ON d.{fk} = h.{pk}
WHERE h.{pk} = '{RECORD_ID}';
```

#### 5b. Recalculate Expected Total
```sql
-- Calculate what the total SHOULD be using raw DB values
SELECT
    {field1},
    {field2},
    ({formula}) AS expected_total,
    {stored_total} AS actual_total,
    ({formula}) - {stored_total} AS delta
FROM {table}
WHERE {pk} = '{RECORD_ID}';
```

#### 5c. Check Orphan Records
```sql
-- Find child records without a parent
SELECT d.*
FROM {detail_table} d
LEFT JOIN {header_table} h ON h.{pk} = d.{fk}
WHERE h.{pk} IS NULL;
```

#### 5d. Check Integrity
```sql
-- Verify header total = sum of detail items
SELECT
    h.{pk},
    h.{total_field} AS header_total,
    SUM(d.{amount_field}) AS sum_details,
    h.{total_field} - SUM(d.{amount_field}) AS delta
FROM {header_table} h
JOIN {detail_table} d ON d.{fk} = h.{pk}
GROUP BY h.{pk}
HAVING ABS(h.{total_field} - SUM(d.{amount_field})) > 0.01;
```

#### 5e. Audit Trail Check
```sql
-- Check recent changes to the record
SELECT * FROM {log_table}
WHERE {entity_id} = '{RECORD_ID}'
ORDER BY {timestamp} DESC
LIMIT 20;
```

---

## Layer 6: Root Cause Classification

Once the faulty layer and code are identified, classify the root cause:

| Category | Risk | Example |
|---|---|---|
| **JS Calculation Error** | P0 — Financial | Wrong formula in client-side calculation |
| **PHP Formula Mismatch** | P0 — Financial | Server formula differs from client formula |
| **Missing Validation** | P1 — Data Integrity | No boundary check (e.g., weight < 0) |
| **Incorrect Query** | P1 — Data Loss | Wrong JOIN, missing WHERE, incorrect aggregate |
| **UI Selector Bug** | P1 — Functional | Panel selector used instead of row selector |
| **Missing Error Handling** | P2 — Stability | No try-catch, silent failure |
| **Configuration Bug** | P2 — Business | Hardcoded value instead of config-driven |
| **Concurrency Issue** | P1 — Data Integrity | Race condition, missing locks |
| ... | ... | ... |

### Bug Ticket Template
```
Bug ID: {MODULE}-{TRACK}{ROUND}{NN}
Title: {Concise description}
Severity: {P0/P1/P2/P3}
Track: {A (System) / B (Business)}
Category: {from table above}
Root Cause Layer: {JS / Controller / Model / DB / Config}
File: {path}
Method: {function_name}
Line: {line_number}
Current Behavior: {what happens now}
Expected Behavior: {what should happen}
Evidence: {DB query results, console logs, screenshots}
```
