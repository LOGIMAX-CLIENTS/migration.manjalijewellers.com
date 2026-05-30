# Recipe: Cancel Bill Leaves Orphan Status Log — Stock Report Over-Count

## Metadata
- **Pattern ID**: PAT-DAT-008
- **Severity**: HIGH
- **Modules Affected**: Billing (cancel_bill), Stock Report (stock_details), Section-wise Stock
- **Auto-fixable**: No (requires tag-specific forensic SQL — never a blanket delete)

## Client Scope
- **Applies to**: ALL
- **Reason**: cancel_bill() does not clean ret_taging_status_log in any client build

## Created By
- **Developer**: Antigravity AI
- **Client**: sriganeshjewels
- **Date**: 2026-04-17
- **Source Bug ID**: N/A (data fix — no code change)

## Symptom

- Stock Report (stock_details) shows **higher closing stock** than Available Stock Design Wise report
- Discrepancy is always a small, fixed number (typically 1–8 pcs) — not random
- Affects only products where the **same bill was cancelled and recreated on the same day**
- Example: Design Wise shows 504 pcs, Stock Report shows 508 pcs (+4 extra)
- O/W (outward) count may appear lower than expected (sold items show in op_stock instead)

## Root Cause

When `cancel_bill()` cancels a bill, it:
- ✅ Correctly resets `ret_taging.tag_status = 0` (tag available again)
- ❌ Does NOT delete the `status=1` sold entry from `ret_taging_status_log`
- ❌ Does NOT delete the `status=6` opening-balance ghost entries written during billing
- ❌ Does NOT write a `status=0` reversal entry to `ret_taging_status_log`

Each billing attempt writes:
- 1× `status=1` entry (sold event) dated with real timestamp
- N× `status=6` entries (opening-balance style) backdated to a fixed historical date (e.g., `2023-03-29`)

These orphan entries survive cancellation. The Stock Report closing stock query:
> "Find each tag's latest `status=0 OR status=6` entry where no later entry exists."

For affected tags, the last entry by ID is a `status=6` (backdated) row. Since its date is historical,
no suppressor entry exists after it → query counts the tag as **still available** even though it's sold.

The backdated `status=6` date means these entries **always pass the date filter** for any report range,
making the inflation permanent until manually cleaned.

## Detection

### Step 1: Find products where both reports disagree

```sql
-- Compare design-wise count vs stock-report count per product at branch
SELECT tag.product_id, rpm.product_name,
  SUM(CASE WHEN tag.tag_status = 0 AND tag.current_branch = {id_branch} THEN 1 ELSE 0 END) as design_wise,
  COUNT(DISTINCT m1.tag_id) as stock_report_count,
  SUM(CASE WHEN tag.tag_status = 0 AND tag.current_branch = {id_branch} THEN 1 ELSE 0 END)
    - COUNT(DISTINCT m1.tag_id) as discrepancy
FROM ret_taging_status_log m1
LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id
    AND m1.id_tag_status_log < m2.id_tag_status_log
    AND date(m2.date) <= CURDATE())
LEFT JOIN ret_taging tag ON tag.tag_id = m1.tag_id
LEFT JOIN ret_product_master rpm ON rpm.pro_id = tag.product_id
WHERE m2.id_tag_status_log IS NULL
  AND m1.to_branch = {id_branch}
  AND m1.status IN (0, 6)
  AND date(m1.date) <= CURDATE()
GROUP BY tag.product_id, rpm.product_name
HAVING discrepancy <> 0
ORDER BY ABS(discrepancy) DESC;
```

### Step 2: For each flagged product — find tags that appear sold in log but have tag_status=0 in ret_taging (or vice versa)

```sql
-- Find tags where status_log says 'available' but tag_status=1 (sold)
-- These tags are the false positives being counted in stock report
SELECT m1.tag_id, rt.tag_code, rt.tag_status, m1.status as log_status, m1.date as log_date
FROM ret_taging_status_log m1
LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id
    AND m1.id_tag_status_log < m2.id_tag_status_log
    AND date(m2.date) <= CURDATE())
LEFT JOIN ret_taging rt ON rt.tag_id = m1.tag_id
WHERE m2.id_tag_status_log IS NULL
  AND m1.to_branch = {id_branch}
  AND m1.status IN (0, 6)
  AND date(m1.date) <= CURDATE()
  AND rt.tag_status = 1  -- tag is sold but log still says available
  AND rt.product_id = {product_id};
```

### Step 3: Trace the tag through billing history — identify which bills are cancelled

```sql
-- Show all bill_details entries for the affected tags
SELECT rbd.tag_id, rb.bill_id, rb.bill_no, rb.bill_status, rb.bill_date,
  CASE rb.bill_status WHEN 1 THEN 'ACTIVE' WHEN 2 THEN 'CANCELLED' ELSE rb.bill_status END as status_label
FROM ret_bill_details rbd
JOIN ret_billing rb ON rb.bill_id = rbd.bill_id
WHERE rbd.tag_id IN ({comma_separated_tag_ids})
ORDER BY rbd.tag_id, rb.bill_date;
```

### Step 4: Show all status_log entries for affected tags (identify orphans)

```sql
SELECT id_tag_status_log, tag_id, status, date, from_branch, to_branch
FROM ret_taging_status_log
WHERE tag_id IN ({comma_separated_tag_ids})
ORDER BY tag_id, id_tag_status_log;
```

## Files

- `admin/application/models/ret_billing_model.php` — `cancel_bill()` function (code gap — NOT fixed here)
- `admin/application/models/ret_reports_model.php` — `get_stock_details()` (stock report query)
- `ret_taging_status_log` — database table with orphan entries (data fix target)

## Fix

This is a **data-only fix** — no code changes. The orphan log entries must be surgically deleted.

> ⚠️ WARNING: Never do a blanket delete. Always identify exact log entry IDs first using the detection queries above. Verify row count before executing DELETE.

### Before (orphan state — tag 278563 example)

```
id_log    | tag_id | status | date
----------+--------+--------+----------------------
1355749   | 278563 | 0      | 2025-10-12 17:31:31   ← Valid: BT arrival
1520642   | 278563 | 1      | 2026-04-02 18:16:43   ← ORPHAN: sold in CANCELLED bill 260768
1520651   | 278563 | 6      | 2023-03-29 00:00:00   ← ORPHAN: ghost entry from bill 260768
1520660   | 278563 | 6      | 2023-03-29 00:00:00   ← ORPHAN: ghost entry from bill 260768
1520715   | 278563 | 1      | 2026-04-02 18:50:11   ← ORPHAN: sold in CANCELLED bill 260778
1520743   | 278563 | 6      | 2023-03-29 00:00:00   ← ORPHAN: ghost entry from bill 260778
1520771   | 278563 | 1      | 2026-04-02 18:58:06   ← KEEP: sold in ACTIVE bill 260782 ✅
1520780   | 278563 | 6      | 2023-03-29 00:00:00   ← ORPHAN: ghost entry from bill 260782
```

### After (clean state)

```
id_log    | tag_id | status | date
----------+--------+--------+----------------------
1355749   | 278563 | 0      | 2025-10-12 17:31:31   ← Valid: BT arrival
1520771   | 278563 | 1      | 2026-04-02 18:58:06   ← Valid: sold in ACTIVE bill ✅
```

### Fix SQL (template — IDs must be replaced with values from detection queries)

```sql
-- STEP 1: Backup
CREATE TABLE ret_taging_status_log_bak_{YYYYMMDD} AS
SELECT * FROM ret_taging_status_log WHERE tag_id IN ({affected_tag_ids});
SELECT COUNT(*) FROM ret_taging_status_log_bak_{YYYYMMDD};  -- verify backup

-- STEP 2: Preview (verify exactly N rows to delete — all from cancelled bills)
SELECT id_tag_status_log, tag_id, status, date
FROM ret_taging_status_log
WHERE id_tag_status_log IN ({orphan_log_ids});

-- STEP 3: Delete orphans (only after step 2 confirms correct rows)
DELETE FROM ret_taging_status_log
WHERE id_tag_status_log IN ({orphan_log_ids});
SELECT ROW_COUNT() AS deleted;  -- must match step 2 count exactly

-- STEP 4: Verify both reports now match
SELECT COUNT(DISTINCT m1.tag_id) as stock_report_count
FROM ret_taging_status_log m1
LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id
    AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date) <= CURDATE())
LEFT JOIN ret_taging tag ON tag.tag_id = m1.tag_id
WHERE m2.id_tag_status_log IS NULL AND m1.to_branch = {id_branch}
  AND m1.status IN (0,6) AND date(m1.date) <= CURDATE() AND tag.product_id = {product_id};

SELECT COUNT(*) as design_wise_count FROM ret_taging
WHERE product_id = {product_id} AND tag_status = 0 AND current_branch = {id_branch};
-- Both counts must be equal ✅
```

### Rollback

```sql
-- Restore from backup if anything goes wrong
INSERT INTO ret_taging_status_log
SELECT * FROM ret_taging_status_log_bak_{YYYYMMDD};
```

## Verification

1. Run detection Step 1 — discrepancy column must show 0 for fixed products
2. Open stock report in browser → filter for affected product → closing stock must match design-wise count
3. Confirm billing records are untouched: `SELECT bill_status FROM ret_billing WHERE bill_id IN ({cancelled_ids})` — still shows 2 (cancelled)
4. Confirm active bill still valid: open the active bill in the UI — all items must display normally
5. Verify `ret_taging.tag_status = 1` for sold tags (unchanged by this fix)

## Notes

- **Deleting status=1 log entries from cancelled bills is SAFE** — the actual sale validity lives in `ret_billing.bill_status`, not in the log table. The log is audit-only for stock report calculations.
- **The code gap (cancel_bill not cleaning status_log) is UNFIXED** — this data fix is a one-time patch. Every future cancel-and-recreate on the same product will create the same orphans. Schedule the `cancel_bill()` code fix as a separate Track A engineering task.
- **status=6 entries are ghost "opening balance" rows** written by the billing module during save. They have a hardcoded backdated date from historical stock migration. Their presence after a sold event is always indicative of this bug.
- **IDs MUST be identified from live database, not local snapshot** — log entry IDs will differ between environments.
- **Only `status=6` entries written AFTER the final valid sale (by ID order) need to be deleted** from the active bill — the ones from cancelled bills must all be deleted.
