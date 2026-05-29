# DB VERIFY QUERIES — Stock Issue Module
> Run these against the database to verify assumptions, audit data integrity, and diagnose bugs.
> 2026-03-19 | Brain Round 5

---

## 1. Schema Verification

### 1.1 Confirm owned table columns exist
```sql
-- ret_stock_issue
DESCRIBE ret_stock_issue;

-- ret_stock_issue_detail  
DESCRIBE ret_stock_issue_detail;
```

### 1.2 Verify issue_no format and uniqueness
```sql
-- Check for duplicate issue_no (R-05 race condition)
SELECT issue_no, COUNT(*) as cnt
FROM ret_stock_issue
GROUP BY issue_no
HAVING cnt > 1;

-- Sample issue_no format (expect: YYYY-YYYY-NNNNN)
SELECT issue_no, fin_year, id_stock_issue
FROM ret_stock_issue
ORDER BY id_stock_issue DESC
LIMIT 10;
```

### 1.3 Verify unique constraint on issue_no
```sql
SHOW INDEX FROM ret_stock_issue WHERE Column_name = 'issue_no';
-- If returns empty: UNIQUE constraint missing → R-05 risk confirmed
```

---

## 2. Data Integrity Checks

### 2.1 Orphaned issue_detail rows (detail with no parent issue)
```sql
SELECT d.id_stock_issue_detail, d.id_stock_issue
FROM ret_stock_issue_detail d
LEFT JOIN ret_stock_issue i ON i.id_stock_issue = d.id_stock_issue
WHERE i.id_stock_issue IS NULL;
-- Any rows = data integrity failure (should be 0)
```

### 2.2 Tagged items with wrong tag_status after issue
```sql
-- Tags that are in issued detail (status=1) but tag_status is NOT 7
SELECT d.tag_id, t.tag_code, t.tag_status, d.status as detail_status
FROM ret_stock_issue_detail d
LEFT JOIN ret_taging t ON t.tag_id = d.tag_id
WHERE d.status = 1
  AND t.tag_status != 7
  AND d.tag_id IS NOT NULL;
-- Should be 0. Any rows = tag_status not updated on issue (or stuck)
```

### 2.3 Received tags not reset to tag_status=0
```sql
-- Tags that show received (detail status=3) but tag_status still=7
SELECT d.tag_id, t.tag_code, t.tag_status, d.status as detail_status
FROM ret_stock_issue_detail d
LEFT JOIN ret_taging t ON t.tag_id = d.tag_id
WHERE d.status = 3
  AND t.tag_status = 7
  AND d.tag_id IS NOT NULL;
-- Should be 0. Any rows = receipt didn't reset tag_status
```

### 2.4 Issues with no detail rows at all
```sql
SELECT i.id_stock_issue, i.issue_no, i.status, i.created_on
FROM ret_stock_issue i
LEFT JOIN ret_stock_issue_detail d ON d.id_stock_issue = i.id_stock_issue
WHERE d.id_stock_issue IS NULL;
-- Flags issues with empty detail (save aborted after header insert?)
```

### 2.5 NULL dates in taging status log (R-07)
```sql
-- Detect NULL dates in tag log (inserted by receipt branch with missing $issue_date)
SELECT id_taging_status_log, tag_id, `date`, created_on
FROM ret_taging_status_log
WHERE `date` IS NULL
ORDER BY id_taging_status_log DESC
LIMIT 20;
```

### 2.6 NULL dates in nontag item log (R-07 extended)
```sql
SELECT id_nontag_item_log, `date`, created_on
FROM ret_nontag_item_log
WHERE `date` IS NULL
ORDER BY id_nontag_item_log DESC
LIMIT 20;
```

---

## 3. OTP Audit (R-09)

### 3.1 Check OTP table structure
```sql
DESCRIBE otp;
-- Expect: mobile, otp_code, otp_gen_time, is_verified, verified_time, module, send_resend, id_emp
```

### 3.2 Recent Stock Issue OTPs (security audit)
```sql
-- Check how long OTPs are being retained
SELECT mobile, otp_code, otp_gen_time, is_verified, verified_time
FROM otp
WHERE module = 'Stock Issue'
ORDER BY otp_gen_time DESC
LIMIT 20;
-- If otp_code is visible in this table, the OTP is stored in plaintext
```

### 3.3 OTPs never verified (abandoned)
```sql
SELECT COUNT(*) as unverified_otps
FROM otp
WHERE module = 'Stock Issue'
  AND is_verified = 0
  AND otp_gen_time < DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

---

## 4. Issue Number Sequence Check

### 4.1 Current sequence per financial year
```sql
SELECT fin_year,
       COUNT(*) as total_issues,
       MAX(CAST(SUBSTRING_INDEX(issue_no, '-', -1) AS UNSIGNED)) as max_seq,
       MIN(CAST(SUBSTRING_INDEX(issue_no, '-', -1) AS UNSIGNED)) as min_seq
FROM ret_stock_issue
GROUP BY fin_year
ORDER BY fin_year DESC;
```

### 4.2 Gaps in issue sequence (concurrent inserts)
```sql
-- Finds gaps in sequential issue numbers (sign of race condition R-05)
SELECT a.seq + 1 AS gap_start
FROM (
  SELECT CAST(SUBSTRING_INDEX(issue_no, '-', -1) AS UNSIGNED) as seq, fin_year
  FROM ret_stock_issue
  WHERE fin_year = (SELECT fin_year_code FROM ret_financial_year WHERE fin_status = 1)
) a
LEFT JOIN (
  SELECT CAST(SUBSTRING_INDEX(issue_no, '-', -1) AS UNSIGNED) as seq
  FROM ret_stock_issue
  WHERE fin_year = (SELECT fin_year_code FROM ret_financial_year WHERE fin_status = 1)
) b ON a.seq + 1 = b.seq
WHERE b.seq IS NULL
ORDER BY gap_start;
```

---

## 5. Tax Verification (R-11)

### 5.1 What tax rates are configured per category/metal?
```sql
SELECT c.name as category, c.hsn_code, m.tax_percentage, m.tax_name
FROM ret_category c
LEFT JOIN ret_taxgroupitems t ON t.tgi_tgrpcode = c.tgrp_id
LEFT JOIN ret_taxmaster m ON m.tax_id = t.tgi_taxcode
WHERE c.status = 1
ORDER BY c.name;
-- If tax_percentage != 3 for any category → R-11 is causing wrong challan amounts
```

---

## 6. Performance Baselines (for N+1 bug impact)

### 6.1 Count active issues (for N+1 severity estimation)
```sql
SELECT COUNT(*) as total_issues,
       SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending,
       SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as issued,
       SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as received
FROM ret_stock_issue;
-- If total_issues > 100: list page is making 100+ queries (R-06 severity HIGH)
```

### 6.2 Average tags per issue (for N+1 print impact)
```sql
SELECT
  AVG(tag_count) as avg_tags_per_issue,
  MAX(tag_count) as max_tags_per_issue
FROM (
  SELECT id_stock_issue, COUNT(*) as tag_count
  FROM ret_stock_issue_detail
  WHERE tag_id IS NOT NULL
  GROUP BY id_stock_issue
) t;
-- High avg_tags: each print call makes avg_tags * 2 extra queries (get_StoneDetails + get_other_metal_details)
```

---

## 7. Stuck/Corrupted Data

### 7.1 Tags stuck in issued state (tag_status=7 but no active issue)
```sql
SELECT t.tag_id, t.tag_code, t.tag_status, d.status as detail_status, d.id_stock_issue
FROM ret_taging t
LEFT JOIN ret_stock_issue_detail d ON d.tag_id = t.tag_id AND d.status = 1
WHERE t.tag_status = 7
  AND d.tag_id IS NULL;
-- Any rows = tag stuck in transit with no active issue record
```

### 7.2 Issues with wrong status vs detail status
```sql
-- Issue says Received (status=3) but detail rows still show Issued (status=1)
SELECT i.id_stock_issue, i.issue_no, i.status as issue_status,
       COUNT(d.id_stock_issue_detail) as detail_rows,
       SUM(CASE WHEN d.status=1 THEN 1 ELSE 0 END) as still_issued
FROM ret_stock_issue i
LEFT JOIN ret_stock_issue_detail d ON d.id_stock_issue = i.id_stock_issue
WHERE i.status = 3
GROUP BY i.id_stock_issue
HAVING still_issued > 0;
```

---

## 8. Profile Settings Verification

### 8.1 Which profiles require OTP for stock issue?
```sql
SELECT id_profile, profile_name, stock_issue_otp_req
FROM profile
ORDER BY profile_name;
-- Verify stock_issue_otp_req column exists (R-04 uses this)
```
