# LESSONS LEARNED — Stock Issue Module
> Created: Round 10 | 2026-03-19 | Cross-module pattern analysis

---

## What This Document Is

After 9 rounds of brain build on the Stock Issue module, these are the **repeating patterns** found across this module — cross-referenced against findings in `Old Metal Process` and `Retail Dashboard` brain builds. These are system-wide anti-patterns, not module-specific bugs.

---

## Pattern 1: Raw SQL Interpolation (System-Wide)

**In this module**: R-02, R-03, R-04, R-13, R-14, R-15, R-16, R-19 — 11 injection vectors  
**Also in**: OMP-001/051/057 (30+ points), Dashboard AP-01 (all model methods)

> This is the #1 systemic vulnerability across the entire codebase.

**Root cause**: All modules in this codebase were written before the team adopted CodeIgniter's Active Record / Query Builder. Every team member who wrote raw `$this->db->query("... WHERE x=".$x)` created an injection point.

**Fix template**:
```php
// ❌ Always found as:
$sql = "SELECT * FROM ret_taging WHERE tag_code='".$tag_code."'";
$this->db->query($sql);

// ✅ Fix with CI Active Record:
$this->db->where('tag_code', $tag_code);
$result = $this->db->get('ret_taging');

// ✅ Or with escape():
$sql = "SELECT * FROM ret_taging WHERE tag_code=".$this->db->escape($tag_code);
```

**Priority**: Fix POST-sourced params first (tag_code, id_branch → R-02, R-03, R-15), then loop-sourced params.

---

## Pattern 2: Debug Code Left in Production (System-Wide)

**In this module**: R-01 (4x `echo last_query();exit`), R-24 (3x `console.log`)  
**Also in**: OMP-036 (21 `console.log`), Dashboard AP-11

**Two sub-variants found**:

| Variant | Example | Risk |
|---|---|---|
| PHP debug echo | `echo $this->db->last_query();exit;` | 🔴 Kills rollback — CRITICAL |
| JS console.log | `console.log(data)` | 🟡 Leaks response data to DevTools |

> The PHP variant in this module is uniquely dangerous: it's inside the `else` branch of `trans_status()`, which means **the rollback never fires** when a transaction fails.

**Fix**: Remove ALL `echo ... exit` from any production path. Use `log_message('debug', ...)` instead. Strip all `console.log` before release.

---

## Pattern 3: N+1 Query in Loop (System-Wide)

**In this module**: R-06, R-20 (3 confirmed patterns)  
**Also in**: Dashboard AP-07 (nested loop — 60 queries per request)

**Pattern recognized**: Fetch a list of records, then query for sub-records per row inside a PHP loop.

```php
// ❌ Anti-pattern in get_StockIssuedItems():
foreach($issues as $issue) {
    $issue['tags'] = $this->stock_issue_tags($issue['id_stock_issue']); // 1 query per row
}

// ✅ Fix: Single JOIN query with GROUP_CONCAT or subquery
```

**Impact**: N+1 on Stock Issue list with 50 issues = 51 queries. Retail Dashboard's variant (N×M loop) scales to 60 queries for 5 branches.

---

## Pattern 4: OTP/Sensitive Data in JSON Response (Module-Specific + Common)

**In this module**: R-09 — OTP returned in `stock_issue_sendotp` response  
**Also in**: Other billing modules (pattern observed in estimation audit)

```json
// ❌ Actual bug in this module:
{"status": true, "msg": "OTP sent", "OTP": "483921"}
```

> Any developer who calls `JSON.stringify(data)` in an error handler or logs `data` will expose the OTP. An attacker with DevTools can bypass OTP entirely.

**Fix**: Never return the OTP. Session holds the truth. JS should only act on `data.status`.

---

## Pattern 5: Client-Side XSS via Unescaped Server Response (JS-Layer)

**In this module**: R-22 — `data.msg` injected raw into OTP modal DOM  
**Also in**: Retail Dashboard AP-02 (indirect), OMP-013 (`$_POST` raw in SQL — server side equivalent)

```javascript
// ❌ Bug R-22:
$(".otp_alert").append('<p style="color:green">' + data.msg + '</p>');

// ✅ Fix:
$('<p>').css('color','green').text(data.msg).appendTo('.otp_alert');
```

> This is a **client-side pattern** first seen in Stock Issue's JS audit. Should be looked for in ALL modules that use `.append(<tag> + serverData + </tag>)`.

---

## Pattern 6: Synchronous AJAX Blocking UI (JS-Layer)

**In this module**: R-23 — `async: false` on OTP send + verify  
**Only confirmed module**: Stock Issue (unique to OTP flows)

```javascript
// ❌ Found at JS L1527 and L1719:
$.ajax({ ..., async: false, ... });

// ✅ Fix: Convert to proper success/error callbacks
$.ajax({
    ...,
    success: function(data) { handleOtpResult(data); },
    error: function() { showError(); }
});
```

> `async: false` is deprecated in modern jQuery and browsers log a warning. It blocks the entire browser tab until the AJAX call resolves — if the SMS gateway is slow, the page freezes.

---

## Pattern 7: Undefined Variable from Branch Splitting

**In this module**: R-07 (`$issue_date` defined in issue branch, used in receipt branch), R-17, R-18  
**Also in**: OMP-024 (`$sales_item_details` undefined for certain trans_type values), Dashboard AP-04

**Root cause**: Variables initialized inside one `if` branch, then used in `else` branch unconditionally.

```php
// ❌ Pattern in Stock Issue:
if($issue_receipt_type == 1) {
    $issue_date = date("Y-m-d H:i:s");  // Defined here
    // ... Tagged Issue logic
}
// else: receipt branch:
$logData['issue_date'] = $issue_date;  // ❌ PHP notice — NULL in DB

// ✅ Fix: Initialize $issue_date = '' before the if/else split
```

---

## Pattern 8: Race Condition in Sequential Number Generation

**In this module**: R-05 — `generateIssueNo()` uses `MAX()` without lock  
**Also in**: OMP-005 — `generate_process_number()` same pattern

> Both modules use `MAX(id)` to generate sequential numbers. Under concurrent requests, both processes may read the same MAX and generate duplicate numbers.

**Systemic fix**: Add DB-level `UNIQUE` constraint on the number column + PHP retry logic, OR use a dedicated sequence table with `SELECT ... FOR UPDATE` locking.

---

## Pattern 9: Transaction Started but Never Rolled Back on Failure

**In this module**: R-01 (echo kills rollback), R-10 (missing rollback in OTP verify)  
**Also in**: OMP-009 (`trans_status()` check timing issue)

```php
// ❌ Pattern: trans_begin() but no rollback on failure path
$this->db->trans_begin();
// ... operations ...
if($this->db->trans_status() === TRUE) {
    $this->db->trans_commit();
} else {
    echo $this->db->last_query(); exit; // ❌ NO ROLLBACK — R-01
}
```

**Fix checklist** for every `trans_begin()`:
1. ✅ `trans_begin()` before first write
2. ✅ `trans_commit()` in success path
3. ✅ `trans_rollback()` in every failure path
4. ✅ No `exit` / `die` / `echo` between begin and commit/rollback

---

## Cross-Module Bug Pattern Heat Map

| Pattern | Stock Issue | Old Metal Process | Retail Dashboard |
|---|---|---|---|
| Raw SQL Concatenation | 🔴 11 vectors | 🔴 30+ vectors | 🔴 All methods |
| Debug Code in Prod | 🔴 7 instances | 🟡 21 console.log | 🟡 All AJAX methods |
| N+1 Queries | 🟠 3 patterns | ✅ Not found | 🟠 1 nested N×M |
| Race Condition (Seq No) | 🟠 1 | 🟠 1 | ✅ Not found |
| Undefined Variables | 🟠 3 | 🟠 2 | 🔴 2 |
| Missing Rollback | 🔴 3 paths | 🟠 1 | ✅ Not found |
| Client-side XSS | 🔴 1 (OTP modal) | ✅ Not found | ✅ Not found |
| Synchronous AJAX | 🟠 2 calls | ✅ Not found | ✅ Not found |
| OTP in JSON | 🔴 1 | ✅ N/A | ✅ N/A |
| Hardcoded Business Value | 🔴 3% GST | 🟠 Piece=1 | ✅ Not found |

---

## Team Recommendations

1. **Immediate**: All new code must use CI Active Record / `$this->db->where()` — no raw concatenation ever
2. **Pre-deploy checklist**: Grep for `console.log` and `echo last_query()` before any release
3. **Transaction template**: Copy the 4-step transaction pattern (begin → commit → rollback → no-echo) as a team snippet
4. **JS safety rule**: Never use `.append('<tag>' + serverData + '</tag>')` — always use `.text()` for server data
5. **Sequential IDs**: Any module generating business keys (issue_no, process_no, etc.) must use UNIQUE constraint + retry
6. **OTP policy**: OTP must NEVER appear in any JSON response key — session-only

---

> Brain build complete. 9 rounds. 24 bugs. 9 patterns documented.  
> Use `/module-bug-audit` to begin systematic fixes. 🚀
