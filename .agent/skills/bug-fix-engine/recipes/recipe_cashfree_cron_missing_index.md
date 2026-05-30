# Recipe: Cashfree Cron UPDATE Missing Index on ref_trans_id

> Cashfree auto-verify cron runs every 30 min and does UPDATE payment WHERE ref_trans_id=? but ref_trans_id is 2nd column in composite index — MySQL can't use it, full table scan on 143K rows every 30 min

## Metadata
- **Pattern ID**: PAT-QUERY-003
- **Severity**: HIGH
- **Modules Affected**: Payment (admin_services.php → verify_cashfreepayment), affects ALL pages via MySQL lock
- **Auto-fixable**: Yes (database index, no code change)
- **Related**: PAT-INFRA-001 (FPM terminate timeout), PAT-QUERY-002 (dashboard N+1)

## Client Scope
- **Applies to**: ALL (any client with Cashfree payment gateway integration)
- **Reason**: The `ref_trans_id` column exists only as 2nd position in a composite index — MySQL ignores it for single-column lookups

## Created By
- **Developer**: Antigravity AI
- **Client**: nskjewels.com
- **Date**: 2026-04-27
- **Source Bug ID**: Recurring 502 Bad Gateway (April 25 slow log evidence)

## Symptom
- PHP-FPM slow log shows `paymentDB()` entries at `payment_model.php:708` every 30 minutes (matching cron schedule)
- Slow log also shows `curl_exec()` at `admin_services.php:3391` — Cashfree API call hanging
- 502 Bad Gateway occurs 1-2 minutes after the cron fires (14:30 cron → 14:32 user gets 502)
- ~48 slow entries per day from cron alone (every 30 min × 24 hours)

## Root Cause
The `verify_cashfreepayment()` function at `admin_services.php:3355` runs as a cron every 30 minutes:

1. Fetches pending/failed Cashfree payments from last 3 days
2. For EACH payment, calls Cashfree API via `curl_exec()` (line 3391)
3. On success, calls `paymentDB("updatestatus", $txn_id, $payres_array)` (line 3637)
4. This triggers `UPDATE payment SET ... WHERE ref_trans_id = ?` (line 707-708)

The `ref_trans_id` column has index `id_transaction_2` but at **Seq_in_index = 2** (second column). MySQL composite indexes only work left-to-right — the first column must be in the WHERE clause. Since the UPDATE only uses `ref_trans_id`, MySQL **cannot use this index** → full table scan on 143K+ rows.

### Why it causes 502
- Cron fires every 30 min → UPDATE locks rows while scanning full table → takes >10 seconds
- During this lock, any user trying to access the payment table (payment/add, payment list) waits
- Combined with dashboard load → all workers busy → Cloudflare timeout → 502

### Slow Log Evidence (April 25, 2026)
```
[25-Apr-2026 14:00:12] pid 715074 → paymentDB() → payment_model.php:708 (>10 sec)
[25-Apr-2026 14:30:12] pid 715083 → curl_exec() → admin_services.php:3391 (>10 sec)
[25-Apr-2026 14:32:25] → User gets 502 on payment/add (2 min after cron)
```

## Detection
```bash
# Check 1: Is ref_trans_id a standalone index?
mysql -e "SHOW INDEX FROM payment WHERE Column_name = 'ref_trans_id';"
# VULNERABLE if: Seq_in_index > 1 (not first column in composite index)

# Check 2: Slow log entries from cron
sudo grep "paymentDB" /var/log/php-fpm-slow.log | tail -20

# Check 3: Slow log entries every 30 min pattern
sudo grep -c "$(date '+%d-%b')" /var/log/php-fpm-slow.log
# If count is ~48 (24h × 2 per hour), cron is the culprit

# Check 4: Payment table size
mysql -e "SELECT COUNT(*) FROM payment;"
```

**Vulnerable if**: `ref_trans_id` is NOT the first column in any index AND payment table has >50K rows.

## Files
- No application code changes needed (index-only fix)
- `admin_services.php:3637` — the UPDATE call
- `payment_model.php:707-708` — the `updatestatus` case in `paymentDB()`

## Fix

### Fix 1: Add standalone index on ref_trans_id

```sql
ALTER TABLE payment ADD INDEX idx_ref_trans_id (ref_trans_id);
```

**Why**: MySQL needs `ref_trans_id` as the FIRST (or only) column in an index to use it for `WHERE ref_trans_id = ?`. The existing composite index `id_transaction_2` has it at position 2 — useless for this query.

## Verification
1. **Immediate**: `SHOW INDEX FROM payment WHERE Key_name = 'idx_ref_trans_id';` — should exist
2. **Slow log (24h later)**: `sudo grep "paymentDB" /var/log/php-fpm-slow.log` — no new entries after fix
3. **30-min pattern gone**: Slow log should no longer show entries at exact :00 and :30 marks
4. **Overall slow count**: Should drop from ~86/day to <5/day (combined with PAT-QUERY-002 fix)

## Notes
- **Composite index position matters** — a column at position 2+ in a composite index is INVISIBLE to queries that don't include the first column. This is a common MySQL misconception.
- **The cron also has a curl timeout issue**: `CURLOPT_TIMEOUT = 8` is set (line 3381) but `CURLOPT_CONNECTTIMEOUT` is missing. If DNS/TCP is slow, curl can still hang beyond 8 seconds. Consider adding `CURLOPT_CONNECTTIMEOUT => 5`.
- **Cross-reference**: Apply PAT-INFRA-001 (FPM terminate timeout) as the safety net. Apply PAT-QUERY-002 (dashboard index) to eliminate the other major slow query source.
- **This recipe + PAT-QUERY-002 together** eliminated 86 slow requests/day on nskjewels.com.
