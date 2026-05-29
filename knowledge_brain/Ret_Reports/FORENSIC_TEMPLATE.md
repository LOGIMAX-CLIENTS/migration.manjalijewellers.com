# Forensic Investigation Template
# Module: Ret_Reports

> **Purpose**: Layer-by-layer investigation cheat sheet for diagnosing bugs in this module.
> **Built**: 2026-03-18 — Round 5 (verified)
> **Key Insight**: This is a READ-ONLY reporting module. Most bugs will be **wrong data displayed** rather than **wrong data saved**.

---

## Layer 1: Symptom Collection

### Common Symptoms in Ret_Reports
| # | Symptom | Likely Layer | Severity |
|---|---|---|---|
| 1 | Report shows wrong totals | Model (SQL query) | P1 |
| 2 | Report shows no data | Model (WHERE clause too restrictive) or JS (parse error) | P1 |
| 3 | Report shows duplicate rows | Model (missing DISTINCT or wrong JOIN) | P2 |
| 4 | Export (PDF/Excel) blank or corrupted | Controller (DOMPDF/PHPExcel) | P2 |
| 5 | Date filter not working | JS (date format mismatch) or Model (wrong date column) | P2 |
| 6 | Branch filter shows all branches' data | Model (missing branch WHERE) | P1 |
| 7 | Access denied / buttons missing | `admin_settings_model->get_access()` returns wrong perms | P2 |
| 8 | DataTable error / JS crash | JS (column count mismatch with data) | P1 |
| 9 | Report takes too long / timeout | Model (missing index or bad JOIN) | P2 |
| 10 | Green tag update fails | Controller `update_green_tag()` — transaction error | P1 |
| 11 | Weight totals don't match | Model aggregation formula or stone_type classification | P0 |
| 12 | GST amounts wrong | Model `get_gst_abstract_*` — tax rate lookup | P0 |

### Data to Collect Immediately
- [ ] Exact report URL and parameters (branch, date range, product)
- [ ] Screenshot of wrong output
- [ ] Expected vs actual values
- [ ] Browser console errors (F12 → Console)
- [ ] Network tab — check AJAX response content (F12 → Network → XHR)
- [ ] Exact DB record IDs mentioned in the report
- [ ] User's branch and role/permissions

---

## Layer 2: Reproduce & Isolate

### Reproduction Checklist
1. [ ] Same date range → same wrong result?
2. [ ] Different date range → same issue or different?
3. [ ] Different branch → issue persists?
4. [ ] Different user → same result? (rule out permissions)
5. [ ] Direct SQL query → same data or different?

### Isolation Questions
- **Date-specific?** → Check if the date range crosses a boundary (month end, financial year)
- **Branch-specific?** → Check if branch filter is applied correctly in SQL
- **Product-specific?** → Check if stone_type classification affects the query
- **Amount-specific?** → Check for rounding errors (especially with GST)
- **Volume-specific?** → Large datasets may cause DataTable rendering issues

---

## Layer 3: Client-Side Trace (JavaScript)

### JS File: `admin/assets/js/ret_reports.js` (~2.73 MB)

### Key Console Log Points
```javascript
// 1. Before AJAX call (find the specific report's load function)
console.log('Report AJAX — sending:', {
    from_date: $('#from_date').val(),
    to_date: $('#to_date').val(),
    id_branch: $('#id_branch').val(),
    id_product: $('#id_product').val()
});

// 2. On AJAX success (check raw response)
console.log('Report AJAX — raw response:', data);
var response = JSON.parse(data);
console.log('Report AJAX — parsed:', response);
console.log('Report AJAX — row count:', response.list ? response.list.length : 'NO LIST');

// 3. Check access object
console.log('Report access:', response.access);
```

### Key Variables to Inspect
| Variable | Where | Expected | How to Check |
|---|---|---|---|
| `response.list` | AJAX success | Array of report rows | Console → check type and length |
| `response.access` | AJAX success | Object with permission flags | Console → inspect object |
| `$('#from_date').val()` | Before AJAX | Date string `DD/MM/YYYY` | Console → check format |
| `oTable` | DataTable instance | DataTable object | Console → `typeof oTable` |

### Network Tab Checks
| Endpoint | Method | Expected Status | Key Response Fields |
|---|---|---|---|
| `admin_ret_reports/{report}/ajax` | POST | 200 | `{list: [...], access: {...}}` |
| `admin_ret_reports/get_Active*` | POST | 200 | Array of dropdown options |

---

## Layer 4: Server-Side Trace (PHP)

### Controller: `admin_ret_reports.php`

### Quick Trace Points
| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Report returns empty | Model | `get_{report}()` | — | Add `echo $this->db->last_query(); exit;` after query |
| Wrong totals | Model | `get_{report}()` | — | Run `last_query()` output in phpMyAdmin, check SUM/GROUP BY |
| Green tag fails | Controller | `update_green_tag()` | L211 | Check `trans_status()` return |
| PDF blank | Controller | `generate_cash_abstract()` | L432 | Check `$html` content before `load_html()` |
| Excel error | Controller | `export_csv()` | L479 | Check if temp file is created |
| Branch filter ignored | Model | `get_{report}()` | — | Check if `id_branch = 0` is treated as "all" |
| Date format wrong | Controller | Various | — | Check `$_POST['from_date']` format vs SQL expectation |

### Model: `ret_reports_model.php`

### Query Investigation Points
| Symptom | Method Pattern | Common Issue |
|---|---|---|
| Wrong row count | Most `get_*` methods | Missing/extra JOIN condition |
| Wrong totals | `getBillDetails()`, `day_transactions_report()` | SUM on wrong column or missing GROUP BY |
| Duplicate rows | `get_stock_details_v1()` | Missing DISTINCT with multiple JOINs |
| Missing records | `get_categorywise_*` | WHERE clause filters too aggressively |
| Slow query | `get_section_wise_*` | No index on `entry_date` or `tag_status` |

---

## Layer 5: Database Verification

### Diagnostic SQL Queries

#### 5a. Verify Report Data Against Raw SQL
```sql
-- Run the model's SQL query directly and compare output
-- Add: echo $this->db->last_query(); exit; after the query in the model
-- Then run the output in phpMyAdmin and compare row count + totals
```

#### 5b. Check Date Range Data Exists
```sql
-- Billing data in range
SELECT COUNT(*) as bill_count, SUM(net_amount) as total
FROM ret_billing
WHERE bill_date BETWEEN '{FROM_DATE}' AND '{TO_DATE}'
AND id_branch = {BRANCH_ID};
```

#### 5c. Stock Balance Verification
```sql
-- Check tag stock counts by section
SELECT s.section_name, COUNT(t.tag_id) as tag_count,
       SUM(t.gross_wt) as total_gwt, SUM(t.net_wt) as total_nwt
FROM ret_taging t
JOIN ret_section s ON s.id_section = t.id_section
WHERE t.tag_status = 1 AND t.id_branch = {BRANCH_ID}
GROUP BY s.id_section;
```

#### 5d. GST Calculation Verification
```sql
-- Verify GST amounts
SELECT b.bill_id, b.net_amount,
       SUM(bd.cgst_amt) as total_cgst,
       SUM(bd.sgst_amt) as total_sgst,
       SUM(bd.igst_amt) as total_igst,
       SUM(bd.item_total) as item_total
FROM ret_billing b
JOIN ret_bill_details bd ON bd.bill_id = b.bill_id
WHERE b.bill_id = '{BILL_ID}'
GROUP BY b.bill_id;
```

#### 5e. Payment Mode Reconciliation
```sql
-- Verify day transaction payment totals
SELECT pay_mode, SUM(pay_amount) as total
FROM ret_billing_payment bp
JOIN ret_billing b ON b.bill_id = bp.bill_id
WHERE b.bill_date = '{DATE}' AND b.id_branch = {BRANCH_ID}
GROUP BY pay_mode;
```

---

## Layer 6: Root Cause Classification

| Category | Risk | Example in Ret_Reports |
|---|---|---|
| **SQL Query Error** | P1 — Wrong Data | Missing JOIN condition → duplicate rows |
| **Date Format Mismatch** | P1 — No Data | JS sends `DD/MM/YYYY`, SQL expects `YYYY-MM-DD` |
| **Missing WHERE Clause** | P1 — Wrong Scope | No branch filter → shows all branches |
| **SUM/GROUP BY Error** | P0 — Financial | Grouping on wrong column → inflated totals |
| **stone_type Misclassification** | P0 — Stock Mismatch | New stone type not handled → wrong weight bucketing |
| **Access Control Bug** | P2 — Security | Wrong access path in `get_access()` call |
| **JS Column Mismatch** | P1 — UI Crash | DataTable expects N columns, data has N±1 |
| **Index Missing** | P2 — Performance | Report timeout on large date ranges |
| **DOMPDF/PHPExcel Error** | P2 — Export | Corrupted PDF/Excel due to malformed HTML |
| **Duplicate Method** | P1 — Logic | `day_transactions_report()` defined twice → wrong one used |

---

## Layer 7: Report Data Integrity Trace

> **Specialized layer for this module**: Since Ret_Reports is read-only, the most critical investigation is whether the SQL query returns correct data.

### 7-Step Data Integrity Check
1. **Identify the model method** being called (from METHOD_INDEX.md)
2. **Extract the SQL query** using `echo $this->db->last_query(); exit;`
3. **Run in phpMyAdmin** with the same parameters
4. **Count rows**: Does row count match what the report shows?
5. **Sum amounts**: Do totals match what the report shows?
6. **Check JOINs**: Are there unexpected duplicates from multiple JOINs?
7. **Check WHERE**: Is the branch/date/product filter correctly applied?

### Common Data Integrity Patterns
| Pattern | Check | Fix |
|---|---|---|
| Report total ≠ manual calculation | Run SUM in phpMyAdmin | Fix SQL aggregation |
| Report shows data from other branches | Check `id_branch` in WHERE | Add missing branch filter |
| Report date range includes extra data | Check date comparison operators `>= <=` vs `> <` | Fix boundary conditions |
| Weight totals are doubled | Check for multiple JOINs on same table | Add DISTINCT or sub-query |

---

## Layer 8: Cross-Module Data Check

> **Specialized layer**: Since Ret_Reports reads from ~22 modules, bugs often originate in upstream data.

### Upstream Data Verification
1. **Is the source data correct?** Check the originating module (Billing, Tagging, etc.)
2. **Was data recently migrated?** Check if column names changed
3. **Is a DB view stale?** Re-create `ret_view_*` views if they reference renamed columns
4. **Was a table schema changed?** Check recent ALTER TABLE statements

### Quick Cross-Module Check
```sql
-- Check if billing data exists for the date range
SELECT COUNT(*) FROM ret_billing WHERE bill_date BETWEEN '{FROM}' AND '{TO}';

-- Check if tags exist for the branch
SELECT COUNT(*) FROM ret_taging WHERE id_branch = {ID} AND tag_status = 1;

-- Check if lot data exists
SELECT COUNT(*) FROM ret_lot_inwards WHERE inward_date BETWEEN '{FROM}' AND '{TO}';
```
