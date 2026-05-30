# Recipe: Dashboard Reorder N+1 Query — getTagging() Full Table Scan Loop

> Live CockPit tab calls getTagging() 2,515 times in a loop, each scanning 121K rows in ret_taging without composite index, causing server overload and 502

## Metadata
- **Pattern ID**: PAT-QUERY-002
- **Severity**: CRITICAL
- **Modules Affected**: Retail Dashboard (ret_dashboard_model), affects ALL pages via server overload
- **Auto-fixable**: Yes (database index, no code change)
- **Related**: PAT-INFRA-001 (FPM terminate timeout), PAT-QUERY-001 (payment subquery scan)

## Client Scope
- **Applies to**: ALL (any client with Retail Dashboard + reorder settings)
- **Reason**: N+1 query pattern — loop calls individual queries instead of JOIN

## Created By
- **Developer**: Antigravity AI
- **Client**: nskjewels.com
- **Date**: 2026-04-27
- **Source Bug ID**: Recurring 502 Bad Gateway

## Symptom
- Intermittent 502 Bad Gateway from Cloudflare, especially when someone opens the Live CockPit dashboard tab
- PHP-FPM slow log shows repeated `getTagging()` entries at `ret_dashboard_model.php:2994`
- Multiple slow log entries within seconds of each other (different PIDs = different users)
- 511 slow requests in 4 days, ~86 per day

## Root Cause
**N+1 Query Anti-Pattern in `getReorderItems()`:**

1. `getReorderItems()` (line 2888) fetches ALL reorder settings → 2,515 rows
2. For EACH row, calls `getTagging()` → 1 query per row against `ret_taging` (121K rows)
3. For EACH row, calls `get_cart_items()` → 1 query per row against `order_cart`
4. **Total: 5,031 queries per single dashboard load**

The `getTagging()` query at line 2990-2994:
```sql
SELECT IFNULL(SUM(t.piece),0) as tot_pcs, sum(t.net_wt), SUM(t.gross_wt)
FROM ret_taging t
WHERE t.tag_status=0 AND t.current_branch=? AND t.product_id=? AND t.design_id=? AND net_wt BETWEEN ? AND ?
```

Individual indexes exist on each column but **no composite index**. MySQL can only use ONE index per table scan, so it picks the best single index and scans/filters the rest.

### Why it causes 502 on OTHER pages
When multiple users open Live CockPit simultaneously:
- User A: 5,031 queries → MySQL busy
- User B: 5,031 queries → MySQL overloaded
- User C opens payment/add → MySQL queue full → Cloudflare timeout → 502

### Trigger
The `get_live_cockpit_dashboard_details()` function in `ret_dashboard.js` fires 24 AJAX calls simultaneously. AJAX #15 (`get_ReorderDetails`) triggers the N+1 loop.

## Detection
```bash
# Check 1: PHP-FPM slow log for getTagging
sudo grep "getTagging" /var/log/php-fpm-slow.log | tail -20

# Check 2: Count reorder settings (how many times the loop fires)
mysql -e "SELECT COUNT(*) FROM ret_reorder_settings WHERE id_product IS NOT NULL AND id_design IS NOT NULL AND id_wt_range IS NOT NULL;"

# Check 3: Check if composite index exists
mysql -e "SHOW INDEX FROM ret_taging WHERE Key_name = 'idx_reorder_lookup';"

# Check 4: Table size
mysql -e "SELECT COUNT(*) FROM ret_taging;"
```

**Vulnerable if**: No composite index on ret_taging for (tag_status, product_id, design_id, current_branch, net_wt) AND reorder settings count > 100.

## Files
- No application code changes needed (index-only fix)
- Future improvement: rewrite N+1 loop into single JOIN query

## Fix

### Fix 1: Add composite index (IMMEDIATE — eliminates 502)

```sql
ALTER TABLE ret_taging ADD INDEX idx_reorder_lookup (tag_status, product_id, design_id, current_branch, net_wt);
```

**Why this column order**: Matches the WHERE clause in getTagging() exactly. `tag_status` first (low cardinality filter), then the specific item identifiers, then `net_wt` for the BETWEEN range.

### Fix 2 (FUTURE): Rewrite N+1 to single JOIN query
Replace the loop in `getReorderItems()` with a single query that JOINs ret_reorder_settings with ret_taging and aggregates. This would reduce 5,031 queries to 2 queries.

## Verification
1. **Immediate**: Run `SHOW INDEX FROM ret_taging WHERE Key_name = 'idx_reorder_lookup';` — should return the index
2. **Slow log check (24h later)**: `sudo grep -c "$(date -d '1 day ago' '+%d-%b')" /var/log/php-fpm-slow.log` — count should drop from ~86 to <5
3. **Slow log content**: `sudo grep "getTagging" /var/log/php-fpm-slow.log` — should show NO new entries after fix date
4. **502 elimination**: Monitor for 48 hours — 502 should not recur

## Diagnostic Evidence (nskjewels.com — April 27, 2026)
- ret_taging: 121,062 rows
- Reorder settings: 2,515 items
- Queries per dashboard load: 5,031
- Slow log entries: 511 in 4 days
- Individual indexes existed but no composite → each getTagging() scanned thousands of rows

## Notes
- **This is an N+1 query problem** — the classic ORM anti-pattern, but in raw SQL
- The composite index makes each individual query instant, but 2,515 round trips still has overhead
- For maximum performance, the N+1 should be rewritten as a JOIN (separate task)
- **Live CockPit tab fires 24 AJAX calls simultaneously** — even with the index, this is heavy. Consider lazy-loading (only load reorder data when user scrolls to that section)
- **Cross-reference**: PAT-INFRA-001 (FPM terminate timeout) is the safety net that prevents ANY slow query from causing 502. Apply both.
