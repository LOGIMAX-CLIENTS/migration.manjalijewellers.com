# Diagnostic Playbook — "When You See X, Suspect Y First"

> **Purpose**: Senior developer diagnostic intuition captured as rules. Check this BEFORE reading code.
> **Used by**: `/fix-single-bug` Step 1 (DIAGNOSE) — check BEFORE code analysis
> **Last Updated**: 2026-03-16
> **Entries**: 25 rules

---

## How to Use This File

1. Read the **symptom** reported by the customer/user
2. Find the matching category below
3. Follow the **"SUSPECT FIRST"** order — investigate #1 before #2 before #3
4. Check **"NEVER DO"** — these are WRONG approaches that waste time

---

## Category 1: "Data is Missing" (Records exist on one side but not the other)

### RULE-DX-001: Payment on gateway but missing in system
**SUSPECT FIRST (in this order):**
1. Webhook delivery — was the webhook received by our server? Check server logs
2. Webhook handler race condition — did 2+ webhooks arrive simultaneously?
3. Duplicate detection false positive — is the handler rejecting a valid payment as "duplicate"?
4. PHP script timeout — did the handler die mid-processing?

**NEVER DO:**
- ❌ NEVER update payment tables directly with SQL
- ❌ NEVER say "fix the query to show it"
- ❌ NEVER assume the data "just wasn't saved"

**WHY:** The data pipeline (Gateway → Webhook → PHP → DB) failed at a specific point. Find WHERE it broke. Don't patch the end result.

---

### RULE-DX-002: Record saved but child records missing
**SUSPECT FIRST:**
1. Transaction safety — was `trans_begin()`/`trans_complete()` used? Did parent INSERT succeed but child INSERT fail silently?
2. `exit` before `trans_rollback` — check if there's an `exit` or `die()` inside the transaction block
3. Array index mismatch — JS sent `item_name[]` but PHP expected `item_name`
4. Loop counter off-by-one — PHP loop processes N-1 items instead of N

**NEVER DO:**
- ❌ NEVER insert child records directly via SQL to "fix" missing data
- ❌ NEVER assume the save function is fine because "it works for other records"

---

### RULE-DX-003: Record exists in DB but not showing in list/report
**SUSPECT FIRST:**
1. Filter/WHERE clause — is a filter hiding the record? (branch, date range, status, soft-delete flag)
2. JOIN eliminates row — is the query using INNER JOIN where LEFT JOIN is needed?
3. Permission/branch filter — is the user's branch restricting visibility?
4. Pagination — is the record on a later page?

**NEVER DO:**
- ❌ NEVER re-insert the record
- ❌ NEVER change the record's data to make it visible

---

## Category 2: "Wrong Value / Wrong Calculation"

### RULE-DX-004: Column shows NaN
**SUSPECT FIRST:**
1. Variable name mismatch — `$item` vs `$items` in foreach (PAT-VAR-001)
2. Undefined variable — PHP returns null, JS treats as NaN
3. String-to-number — DB returns string "abc", JS `parseFloat("abc")` = NaN
4. Division by zero — denominator is 0 or undefined

**RIPPLE CHECK (mandatory):**
- [ ] Subtotal that sums this column → also NaN?
- [ ] Grand total → also NaN?
- [ ] Footer callback → wrong column index? (PAT-DT-001)
- [ ] Print/PDF → same data source?
- [ ] Export → same data source?

**NEVER DO:**
- ❌ NEVER fix only the one column — ALWAYS check all downstream totals

---

### RULE-DX-005: Column shows 0 instead of actual value
**SUSPECT FIRST:**
1. Missing query — variant function doesn't have the query that populates this field (PAT-RPT-001)
2. Wrong column name — query uses `weight` but column is `nwt`
3. `+=` vs `=` — value is overwritten instead of accumulated
4. Condition skips population — `if ($type == 'tagged')` but record is non-tag

**NEVER DO:**
- ❌ NEVER hardcode the expected value
- ❌ NEVER change the display to hide the 0

---

### RULE-DX-006: Total/amount is wrong (financial calculation bug)
**SUSPECT FIRST:**
1. JS calculation — 90% of financial calculations happen in JS, NOT PHP. Check browser first
2. Variable name — is the JS using the right form field ID?
3. Formula error — is tax inclusive vs exclusive? Is discount applied before or after tax?
4. Both layers — does JS formula match PHP formula? (they can diverge)

**RIPPLE CHECK:**
- [ ] Check JS calculation (browser console)
- [ ] Check PHP re-calculation (if exists)
- [ ] Check what's stored in DB
- [ ] Check what's displayed in view/print
- [ ] If any layer differs → THAT's where the bug is

**NEVER DO:**
- ❌ NEVER update the DB total directly
- ❌ NEVER fix only JS without checking if PHP also calculates the same value
- ❌ NEVER assume the formula is correct just because "it works for other bill types"

---

### RULE-DX-007: GST/tax amount is wrong
**SUSPECT FIRST:**
1. Inclusive vs Exclusive — is `tax_inclusive` flag set correctly?
2. Rate lookup — is the tax rate pulled from the right category/product?
3. Rounding — `round()` vs `floor()` vs `ceil()` — ₹0.01 differences multiply across items
4. Old metal — is old metal purchase amount being deducted before GST calculation?

**NEVER DO:**
- ❌ NEVER change the tax rate in the database
- ❌ NEVER fix the display without fixing the calculation

---

## Category 3: "Feature Works Sometimes, Fails Sometimes"

### RULE-DX-008: Works for user A, fails for user B
**SUSPECT FIRST:**
1. Hardcoded user ID check — `if ($uid != 169 && $uid != 1)` (known pattern in this system)
2. Permission/role — different user roles trigger different code paths
3. Branch-specific data — user A's branch has the data, user B's doesn't
4. Browser/cache — user A cleared cache, user B is seeing stale JS

**NEVER DO:**
- ❌ NEVER add another hardcoded user ID exception

---

### RULE-DX-009: Works on Add, fails on Edit
**SUSPECT FIRST:**
1. Edit path is stale copy of Add — update path missing recent changes from save path (PAT-VAL-002)
2. Edit doesn't load all data — some fields not populated when loading for edit
3. Null-guard mismatch — save has `!empty() ? value : NULL`, edit passes raw empty string
4. JS re-initialization — Add starts fresh, Edit pre-fills but JS isn't re-triggered

**NEVER DO:**
- ❌ NEVER fix only the Edit path — check if Add has the same fix already and copy the pattern

---

### RULE-DX-010: Works on desktop, fails on mobile/API
**SUSPECT FIRST:**
1. API endpoint is stale copy of desktop controller (PAT-BIL-001)
2. Different view file — API uses old view, desktop uses new view
3. Missing data variables — API passes 5 variables, desktop passes 15
4. CSS/layout — DOMPDF doesn't support flexbox, mobile viewport different

---

### RULE-DX-011: Intermittent failure (works 8/10 times)
**SUSPECT FIRST:**
1. Race condition — two requests hitting the same resource simultaneously
2. Session timeout — long form fills → session expires between start and save
3. Network timeout — AJAX call takes >30s, browser gives up
4. Database lock — another process holds a lock on the same row

**NEVER DO:**
- ❌ NEVER say "cannot reproduce, closing" — intermittent bugs are ALWAYS real
- ❌ NEVER add a simple retry without understanding WHY it fails

---

## Category 4: "Delete/Cancel Doesn't Work Properly"

### RULE-DX-012: Delete parent but child records remain (orphans)
**SUSPECT FIRST:**
1. Check `_SYSTEM/CLEANUP_GAPS.md` — there are 11+ KNOWN cleanup gaps already
2. No child table delete — parent deleted but child table DELETE not in the code
3. Status flag not reversed — parent deleted but `tag_status` not reset
4. Cross-module orphan — billing delete doesn't reset `estimation.purchase_status`

**NEVER DO:**
- ❌ NEVER delete orphan records manually without understanding why they exist
- ❌ NEVER fix only the parent delete — ALWAYS check ALL child tables

---

### RULE-DX-013: Cancel/reverse doesn't restore original state
**SUSPECT FIRST:**
1. Check `_SYSTEM/TAG_STATUS_MAP.md` — what status should be restored?
2. Status not reverted — `tag_status` changed from 0→1 on sale, but cancel doesn't change 1→0
3. Quantity not restored — stock qty decremented on sale, not incremented on cancel
4. Log entry not created — status_log should record the cancellation

---

## Category 5: "Report/Print Issues"

### RULE-DX-014: Report shows wrong numbers
**SUSPECT FIRST:**
1. Run the raw SQL query — does the SQL return correct numbers?
2. If SQL wrong → missing JOIN, wrong WHERE filter, missing subquery
3. If SQL right but display wrong → view formatting, number_format(), toFixed()
4. Check variant function (PAT-RPT-001) — is this the `_stone` or `_nontag` variant that's missing a query?

---

### RULE-DX-015: Print layout broken (misaligned, overflow, cut off)
**SUSPECT FIRST:**
1. Find a WORKING print of the same type — copy CSS from there
2. DOMPDF limitation — no flexbox, limited CSS3
3. Column width — did someone add a column? All subsequent indices shift (PAT-DT-001)
4. Image overflow — no `max-width`/`max-height` on image cells

**NEVER DO:**
- ❌ NEVER guess CSS values — always copy from a working reference
- ❌ NEVER change multiple CSS properties at once — one at a time, verify each

---

## Category 6: "Integration / API / Webhook Issues"

### RULE-DX-016: Third-party API returns success but our system shows error
**SUSPECT FIRST:**
1. Response parsing — are we reading the right field from the API response?
2. HTTP status vs body — API returns 200 but error in JSON body
3. Timeout — our code timed out before reading the full response
4. SSL/certificate — curl fails silently on certificate issues

---

### RULE-DX-017: Webhook received but data not processed
**SUSPECT FIRST:**
1. Authentication — is webhook signature verification failing?
2. Payload format changed — API provider updated their payload structure
3. Handler exception — PHP throws error but catches it silently
4. Duplicate detection — handler thinks this is a retry and skips it

---

### RULE-DX-018: Multiple webhooks for same event
**SUSPECT FIRST:**
1. Idempotency — does handler use payment_id or order_id as unique key?
2. Retry storm — our server returned 5xx, gateway retried multiple times
3. Both webhook AND polling — system captures payment via both paths

---

## Category 7: "Concurrency / Race Conditions"

### RULE-DX-019: Duplicate records created
**SUSPECT FIRST:**
1. Double submit — user clicked save twice before response came back
2. No unique constraint — DB allows duplicate entries
3. No JS debounce — button not disabled after first click
4. AJAX race — two AJAX calls complete and both trigger save

---

### RULE-DX-020: Stock quantity goes negative or wrong
**SUSPECT FIRST:**
1. No locking — multiple modules modify `ret_nontag_items.qty` without `SELECT FOR UPDATE`
2. Check timing — did billing and branch transfer happen within seconds of each other?
3. Cancel not reversing — sold item cancelled but qty not incremented back

---

## Category 8: "Security / Permission Issues"

### RULE-DX-021: User sees data they shouldn't
**SUSPECT FIRST:**
1. Missing branch filter — query doesn't filter by user's branch_id
2. Direct URL access — no permission check in controller method
3. AJAX endpoint exposed — data endpoints don't verify session/role

---

### RULE-DX-022: SQL injection / XSS
**SUSPECT FIRST:**
1. Raw `$_POST` in query — no `$this->input->post()` or parameterized query
2. No `htmlspecialchars()` on output — stored XSS via customer name/address
3. Raw `db->query()` with string concatenation — 256 instances in billing model alone

---

## Category 9: "Performance Issues"

### RULE-DX-023: Page loads slowly (>5 seconds)
**SUSPECT FIRST:**
1. N+1 query — loop that runs a query per row instead of batch query
2. Missing index — query does full table scan on million-row table
3. No pagination — loading ALL records instead of paginated
4. Large JS file — 15K+ line JS file loaded without minification

---

### RULE-DX-024: AJAX call hangs / times out
**SUSPECT FIRST:**
1. PHP script timeout — heavy processing exceeds 30s default
2. Database lock — another process holds a lock
3. Infinite loop — recursive function without base case
4. Large response — returning too much data in JSON

---

## Category 10: "Configuration / Environment Issues"

### RULE-DX-025: Feature works in dev, fails in production
**SUSPECT FIRST:**
1. PHP version mismatch — dev has PHP 7.4, production has 8.0 (or vice versa)
2. File permissions — `mkdir 0777` works on Windows, fails on Linux
3. Path separator — `\` vs `/` in file paths
4. Config difference — `ret_settings` table has different values in production
5. Day closing — day not closed, date filter excludes today

---

## Update Rules

1. After every bug fix where initial diagnosis was WRONG → add the correct symptom→suspect mapping here
2. Run `/learn-and-improve` Step 2c to check if playbook needs updating
3. Goal: 50+ rules within 3 months
