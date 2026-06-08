# Recipe: Section Stock In/Out Report — Slow Query Performance Optimization

## Metadata
- **Pattern ID**: PAT-PERF-008
- **Severity**: CRITICAL
- **Modules Affected**: Ret_Reports (Section Stock In/Out, PDF Print, HO Daily Stock Book)
- **Auto-fixable**: No (multi-layer optimization: indexes + code + query restructuring)

## Client Scope
- **Applies to**: ALL
- **Reason**: `get_section_wise_stock_inout_details()` is shared across all clients — the anti-patterns (date() wrapping, redundant subqueries, missing indexes) exist in every deployment

## Created By
- **Developer**: Antigravity
- **Client**: Manepally (production slow query log identified the issue)
- **Date**: 2026-06-07
- **Source Bug ID**: N/A (discovered via slow query log analysis)

## Symptom
- Section-wise stock in/out report takes **9-16 minutes** to load
- Production slow query log shows: avg 548s, max 960s, **46.5 million rows examined** per execution
- 26 executions/hour causing cascading DB slowdowns
- PDF print and HO daily stock book also affected (same model function)

## Root Cause
Five compounding performance anti-patterns in `get_section_wise_stock_inout_details()`:

### 1. Non-sargable date conditions (critical)
All 49 date comparisons wrapped in `date()` function, preventing MySQL from using ANY index:
```sql
-- BEFORE: Index CANNOT be used (function wrapping)
WHERE date(m1.date) BETWEEN '2026-06-01' AND '2026-06-04'
AND date(m1.date) <= '2026-06-03'
```
EXPLAIN shows `type=ALL` (full table scan) even with indexes present.

### 2. Missing composite indexes
No composite indexes on `ret_section_tag_status_log`, `ret_billing`, `ret_bill_details`, `ret_taging` for the query patterns used.

### 3. Redundant subqueries (28 duplicates)
43 LEFT JOIN subqueries where groups of 4 query the SAME tables with the SAME joins/filters, differing only in the `stone_type` filter. Each subquery scans the entire table independently.

### 4. Suboptimal index selection
MySQL optimizer chooses `Idxsectiontobrch` (4,822 rows) over the better `idx_to_branch_status_date` (965 rows) for single-status queries.

### 5. SQL injection vulnerability
All 8 `$data[]` parameters concatenated directly into SQL strings without sanitization.

## Detection
```powershell
# Find the function
findstr /n "function get_section_wise_stock_inout_details" "admin\application\models\ret_reports_model.php"

# Check for date() wrapping (non-sargable)
findstr /n "date(m1.date)" "admin\application\models\ret_reports_model.php"
findstr /n "date(m2.date)" "admin\application\models\ret_reports_model.php"

# Check for redundant stone_type subqueries
findstr /n "stone_type = 1" "admin\application\models\ret_reports_model.php"
findstr /n "stone_type = 2" "admin\application\models\ret_reports_model.php"
findstr /n "stone_type = 3" "admin\application\models\ret_reports_model.php"

# Check for SQL injection (direct $data[] in SQL)
findstr /rn "$data\[" "admin\application\models\ret_reports_model.php" | findstr /i "AND\|WHERE\|JOIN\|branch\|section"
```

## Files
- `admin/application/models/ret_reports_model.php` — New optimized function + original preserved
- `admin/application/controllers/admin_ret_reports.php` — Toggle at 3 call sites
- `database/migrations/20260607_000001_stock_report_performance_indexes.sql` — 8 new indexes

## Fix

### Layer 1: Composite Indexes (Migration SQL)

```sql
-- ret_section_tag_status_log (the hot table — millions of rows)
CREATE INDEX idx_tag_status_date ON ret_section_tag_status_log(tag_id, id_sec_tag_status_log, date);
CREATE INDEX idx_to_branch_status_date ON ret_section_tag_status_log(to_branch, status, date);
CREATE INDEX idx_from_branch_status_date ON ret_section_tag_status_log(from_branch, status, date);
CREATE INDEX idx_to_section_status_date ON ret_section_tag_status_log(to_section, status, date);

-- ret_billing
CREATE INDEX idx_bill_status_date_branch ON ret_billing(bill_status, bill_date, id_branch);

-- ret_bill_details
CREATE INDEX idx_tag_bill_product ON ret_bill_details(tag_id, bill_id, product_id);

-- ret_taging
CREATE INDEX idx_product_section_branch ON ret_taging(product_id, id_section, current_branch, tag_status);

-- ret_ledger
CREATE INDEX idx_date_branch_tag ON ret_ledger(date, from_branch, to_branch, tag_id);
```

### Layer 2: Sargable Date Conditions (49 changes)

#### Before (Opening Balance pattern):
```php
LEFT JOIN ret_section_tag_status_log m2 ON (m1.tag_id = m2.tag_id 
    AND m1.id_sec_tag_status_log < m2.id_sec_tag_status_log 
    AND date(m2.date) <= '".$ToDt."')
WHERE m2.id_sec_tag_status_log IS NULL 
    AND (m1.status = 0 OR m1.status = 6) AND date(m1.date) <= '".$ToDt."'
```

#### After:
```php
// Pre-compute next-day dates at function entry
$op_blc_next_day = date('Y-m-d', strtotime($ToDt . ' +1 day'));
$ToDt_next = date('Y-m-d', strtotime($ToDt . ' +1 day'));

LEFT JOIN ret_section_tag_status_log m2 ON (m1.tag_id = m2.tag_id 
    AND m1.id_sec_tag_status_log < m2.id_sec_tag_status_log 
    AND m2.date < '".$op_blc_next_day."')
WHERE m2.id_sec_tag_status_log IS NULL 
    AND m1.status IN (0, 6) AND m1.date < '".$op_blc_next_day."'
```

#### Before (Date range pattern):
```php
AND date(m1.date) BETWEEN '".$FromDt."' AND '".$ToDt."'
AND date(bill.bill_date) BETWEEN '".$FromDt."' AND '".$ToDt."'
```

#### After:
```php
AND m1.date >= '$FromDt' AND m1.date < '$ToDt_next'
AND bill.bill_date >= '$FromDt' AND bill.bill_date < '$ToDt_next'
```

### Layer 3: SQL Injection Hardening

#### Before (at function entry):
```php
function get_section_wise_stock_inout_details_optimized($data) {
    $FromDt = $data['from_date'];
    // ... $data['id_branch'] concatenated directly into SQL 159 times
```

#### After:
```php
function get_section_wise_stock_inout_details_optimized($data) {
    // --- Input Sanitization ---
    $d1 = date_create($data['from_date']);
    $FromDt = $d1 ? $d1->format('Y-m-d') : date('Y-m-d');
    $d2 = date_create($data['to_date']);
    $ToDt = $d2 ? $d2->format('Y-m-d') : date('Y-m-d');
    
    $id_branch   = intval($data['id_branch'] ?? 0);
    $id_section  = !empty($data['id_section']) && is_array($data['id_section']) 
                     ? implode(',', array_map('intval', $data['id_section'])) : '';
    $id_metal    = !empty($data['id_metal']) && is_array($data['id_metal']) 
                     ? implode(',', array_map('intval', $data['id_metal'])) : '';
    $id_category = !empty($data['id_category']) && is_array($data['id_category']) 
                     ? implode(',', array_map('intval', $data['id_category'])) : '';
    $pro_id      = !empty($data['pro_id']) && is_array($data['pro_id']) 
                     ? implode(',', array_map('intval', $data['pro_id'])) : '';
    $group_by    = intval($data['group_by'] ?? 0);
    $id_stone    = !empty($data['id_stone']) && is_array($data['id_stone']) 
                     ? implode(',', array_map('intval', $data['id_stone'])) : '';
    $id_sub_stone = !empty($data['id_sub_stone']) && is_array($data['id_sub_stone']) 
                      ? implode(',', array_map('intval', $data['id_sub_stone'])) : '';
```

### Layer 4: Subquery Merge — SUM(CASE WHEN) Consolidation

28 subqueries merged into 7 using `SUM(CASE WHEN stone_type=X)` pattern.

#### Before (4 separate subqueries for Sold):
```sql
-- s (sold, no stone filter)
LEFT JOIN(SELECT ... FROM ret_bill_details d LEFT JOIN ret_billing bill ... 
    WHERE bill.bill_status = 1 AND d.item_type=2 AND ... GROUP BY ...) s

-- grm_s (sold, stone_type=1) 
LEFT JOIN(SELECT ... FROM ret_bill_details d LEFT JOIN ret_billing bill ... LEFT JOIN ret_stone st ...
    WHERE bill.bill_status = 1 AND st.stone_type = 1 AND ... GROUP BY ...) grm_s

-- ct_s (sold, stone_type=2)
LEFT JOIN(SELECT ... FROM ret_bill_details d LEFT JOIN ret_billing bill ... LEFT JOIN ret_stone st ...
    WHERE bill.bill_status = 1 AND st.stone_type = 2 AND ... GROUP BY ...) ct_s

-- looseDia_s (sold, stone_type=3)
LEFT JOIN(SELECT ... FROM ret_bill_details d LEFT JOIN ret_billing bill ... LEFT JOIN ret_stone st ...
    WHERE bill.bill_status = 1 AND st.stone_type = 3 AND ... GROUP BY ...) looseDia_s
```

#### After (1 merged subquery):
```sql
LEFT JOIN(SELECT 
    IFNULL(SUM(CASE WHEN st.stone_type = 1 THEN s.wt ELSE 0 END),0) as sold_diawt,
    IFNULL(SUM(CASE WHEN st.stone_type = 2 THEN s.wt ELSE 0 END),0) as sold_ct_wt,
    IFNULL(SUM(CASE WHEN st.stone_type = 3 THEN s.wt ELSE 0 END),0) as sold_loose_dia_wt,
    SUM(d.gross_wt) as gross_wt, SUM(d.net_wt) as net_wt, SUM(d.piece) as piece,
    d.product_id, p.cat_id, d.id_section, '0' as tag_count
    FROM ret_bill_details d
    LEFT JOIN ret_billing bill ON bill.bill_id = d.bill_id
    LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
    LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
    LEFT JOIN ret_billing_item_stones s ON s.bill_det_id = d.bill_det_id
    LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
    WHERE bill.bill_status = 1 AND d.item_type=2 AND ...
    GROUP BY ...) sold_all
```

**All 7 merge groups follow this pattern:**

| Group | Old Subqueries | New Name | Table |
|---|---|---|---|
| 1 | blc, blc_sold, grm_blc, ct_blc | `blc_all` | `ret_section_tag_status_log` |
| 2 | INW, GRM_INW, CT_INW, looseDia_INW | `INW_all` | `ret_section_tag_status_log` |
| 3 | s, grm_s, ct_s, looseDia_s | `sold_all` | `ret_bill_details` |
| 4 | pur_ret, pur_ret_grm, pur_ret_ct, pur_ret_looseDia | `pur_ret_all` | `ret_ledger` |
| 5 | br_out, grm_br_out, ct_br_out, looseDia_br_out | `br_out_all` | `ret_section_tag_status_log` |
| 6 | sect_out, grm_sect_out, ct_sect_out, looseDia_sect_out | `sect_out_all` | `ret_home_section_item_log` |
| 7 | kar_iss, kar_iss_grm, kar_iss_ct, kar_iss_looseDia | `kar_iss_all` | `ret_ledger` |

### Layer 5: USE INDEX Hints

#### Before:
```php
FROM ret_section_tag_status_log m1
-- Optimizer picks Idxsectiontobrch (4,822 rows) — suboptimal
```

#### After:
```php
FROM ret_section_tag_status_log m1 USE INDEX(idx_to_branch_status_date)
-- Forces better index (965 rows) — 80% fewer scans
```

Applied to 8 subqueries total (4 in main block + 4 in group_by=1 block):
- `INW_all`, `STONE_INW`, `sect_out_all`, `stn_sect_out` (main block)
- `INW`, `STONE_INW`, `sect_out`, `stn_sect_out` (group_by=1 block)

**NOT applied to** `blc_all`/`blc` (OR on status breaks composite index) and `br_out_all` (already optimal at 102 rows).

### Controller Toggle (3 call sites)

#### Before:
```php
$result = $this->ret_reports_model->get_section_wise_stock_inout_details($data);
```

#### After:
```php
$use_legacy = $this->input->post('use_legacy');
if ($use_legacy == '1') {
    $result = $this->ret_reports_model->get_section_wise_stock_inout_details($data);
} else {
    $result = $this->ret_reports_model->get_section_wise_stock_inout_details_optimized($data);
}
```

Applied at all 3 call sites:
- `section_stock_inout()` (AJAX report)
- `stock_details_print()` (PDF print)
- `ho_daily_stock_book()` (HO daily book)

## Verification
1. Load section-wise stock report → must return identical data to old function
2. Run with `use_legacy=1` POST param → both must return same results
3. `EXPLAIN` on key subqueries → `type=ref` (not `ALL`), `key=idx_to_branch_status_date`
4. PDF print → renders correctly with same layout/data
5. HO daily stock book → data matches
6. Monitor slow query log for 24h → query time should drop from 548s avg to <30s
7. `php -l admin/application/models/ret_reports_model.php` → No syntax errors

## EXPLAIN Improvement (retail_dev)

| Metric | BEFORE | AFTER | Improvement |
|---|---|---|---|
| m1 access type | `ALL` (full table scan) | `ref` (index lookup) | ✅ Eliminated scan |
| m1 key used | `NULL` (no index) | `idx_to_branch_status_date` | ✅ Index used |
| m1 rows scanned | 11,976 | 965 | **-92%** |
| Subqueries | 43 | 15 | **-65%** (28 eliminated) |
| Row reads (est.) | 46.5M/query | ~4.6M/query (est.) | **-90%** |
| date() function calls | 49 | 0 | **-100%** |
| SQL injection points | 159 | 0 | **-100%** |

## Notes
- **Original function preserved** at its original line number — not deleted or modified
- **Toggle mechanism** allows instant rollback via `use_legacy=1` POST parameter
- **group_by=1 block** (section-only aggregation) also optimized with same index hints and bill_data merge
- **OR on status** (`status = 0 OR status = 6`) prevents composite index use for `blc_all` — this is a MySQL optimizer limitation, not a code issue. A covering index on `(status, to_branch, date)` doesn't help because OR prevents range scan.
- **Related recipe**: `recipe_section_stock_inout_date_and_category_fix.md` (PAT-RPT-002) fixes a different bug (functional, not performance) in the same function — date parsing format and null id_category handling
- **Future optimization**: Self-join anti-pattern (m1/m2 for "latest record") could be replaced with `ROW_NUMBER() OVER (PARTITION BY tag_id ORDER BY id DESC)` on MySQL 8+ for another 8-10x speedup
