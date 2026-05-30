# Investigation Templates — SQL & Commands

> Pre-built queries AI generates during debugging. Replace `{placeholders}`.

---

## 1. Record Lifecycle Trace
```sql
-- Trace a specific record's full history
SELECT * FROM {table} WHERE {id_field} = {id_value};
SELECT * FROM log_detail WHERE record = '{id_value}' ORDER BY id_log_detail DESC LIMIT 20;
```

## 2. Orphan Detection
```sql
-- Find child records whose parent doesn't exist
SELECT child.* FROM {child_table} child
LEFT JOIN {parent_table} parent ON child.{fk} = parent.{pk}
WHERE parent.{pk} IS NULL;
```

## 3. Bill Integrity Check
```sql
-- Compare bill header vs detail totals
SELECT b.id_billing, b.tot_bill_amount,
       SUM(d.total_amount) as detail_total,
       b.tot_bill_amount - SUM(d.total_amount) as diff
FROM ret_billing b
JOIN ret_bill_detail d ON b.id_billing = d.id_billing
WHERE b.id_billing = {id}
GROUP BY b.id_billing;
```

## 4. Payment Reconciliation
```sql
-- Check payment received vs bill amount
SELECT b.id_billing, b.tot_bill_amount, b.tot_amt_received,
       SUM(p.amount) as actual_payments,
       b.tot_amt_received - SUM(p.amount) as payment_diff
FROM ret_billing b
LEFT JOIN ret_bill_pay_device p ON b.id_billing = p.id_billing
WHERE b.id_billing = {id}
GROUP BY b.id_billing;
```

## 5. Tag Status Audit
```sql
-- Check tag_status consistency
SELECT tag_status, COUNT(*) FROM ret_taging 
WHERE id_billing = {id} GROUP BY tag_status;

-- Find tags stuck in wrong status
SELECT t.id_taging, t.tag_status, b.bill_status, b.bill_type
FROM ret_taging t
JOIN ret_billing b ON t.id_billing = b.id_billing
WHERE b.bill_status = 2 AND t.tag_status NOT IN (0, 1);
-- bill_status=2 (cancelled) but tag still in sold/transit status
```

## 6. Journal Verification
```sql
-- Check if journal entries exist for a bill
SELECT * FROM ret_journal WHERE bill_id = {id};

-- Check for unbalanced journals (debit ≠ credit)
SELECT bill_id, 
       SUM(debit_amt) as total_debit, 
       SUM(credit_amt) as total_credit,
       SUM(debit_amt) - SUM(credit_amt) as imbalance
FROM ret_journal
WHERE entry_date BETWEEN '{start}' AND '{end}'
GROUP BY bill_id
HAVING imbalance != 0;
```

## 7. Cross-Module Write Detection
```sql
-- Who wrote to this record? Check log
SELECT module, operation, event_date, id_log 
FROM log_detail 
WHERE record LIKE '%{id_value}%' 
ORDER BY event_date DESC LIMIT 20;
```

## 8. Settings Value Check
```sql
-- Dump all relevant settings
SELECT * FROM ret_settings LIMIT 1\G
-- Check specific setting keys
SELECT {key_name} FROM ret_settings LIMIT 1;
```

## 9. Date/Branch Filter Verification
```sql
-- Common report filter sanity check
SELECT MIN(entry_date), MAX(entry_date), COUNT(*) 
FROM ret_billing 
WHERE id_branch = {branch_id} 
  AND entry_date BETWEEN '{start}' AND '{end}'
  AND bill_status != 2;
```

## 10. Duplicate Detection
```sql
SELECT {field}, COUNT(*) as cnt 
FROM {table} 
GROUP BY {field} 
HAVING cnt > 1 
ORDER BY cnt DESC LIMIT 20;
```

---

## Command Templates

### PHP syntax check
```bash
php -l admin/application/controllers/{file}.php
php -l admin/application/models/{file}.php
```

### Git recent changes
```bash
git log -10 --oneline -- admin/application/controllers/{file}.php
git diff HEAD~1 -- admin/application/controllers/{file}.php
```

### Grep for pattern
```bash
grep -rn "{pattern}" admin/application/ --include="*.php"
grep -rn "{function_name}" admin/application/ --include="*.php" -l
```

### Table structure
```sql
SHOW CREATE TABLE {table}\G
SHOW INDEX FROM {table};
EXPLAIN SELECT {the_slow_query};
```
