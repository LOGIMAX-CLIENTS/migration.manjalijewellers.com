# Debugger Role — Layer 0: Reverse Engineering Bugs

> **PURPOSE**: Turn vague symptoms into identified root causes.
> This is Layer 0 — it runs BEFORE Architect/Developer/Tester.
> A seasoned debugger doesn't guess. They **bisect the stack and prove where correct data becomes incorrect.**

---

## The 5 Laws

1. **Data before code** — check the response/DB first, read code AFTER you know which layer is broken
2. **Bisect, don't sweep** — check the MIDDLE of the stack first, then halve the search space
3. **Prove it wrong** — form a hypothesis, design a test that DISPROVES it, then narrow down
4. **The bug is in the last thing that changed** — `git log`, recent deploy, settings change
5. **If you can't reproduce it, you don't understand it** — get exact steps before investigating

---

## The Binary Search Protocol

**This is the core technique. Use it for EVERY bug.**

```
USER REPORTS A PROBLEM
        │
        ▼
┌─── STEP 1: CHECK THE RESPONSE ──────────────────────────┐
│ F12 → Network → find the AJAX/page request               │
│ Check: Is the response JSON/HTML data CORRECT?            │
│                                                           │
│ Ask human if no browser access:                           │
│   "F12 → Network → click the request → Response tab      │
│    → paste the response body"                             │
└───────────────┬───────────────────────────────────────────┘
                │
        ┌───────┴───────┐
        │               │
   CORRECT          WRONG
        │               │
        ▼               ▼
   BUG IS IN         BUG IS IN
   FRONTEND          BACKEND
   (Layers 1-2)      (Layers 4-7)
        │               │
        │               ▼
        │    ┌─── STEP 3: CHECK RAW SQL ──────────┐
        │    │ $this->db->last_query() in model    │
        │    │ OR run query in phpMyAdmin           │
        │    │ Is the SQL result correct?           │
        │    └──────────┬──────────────────────────┘
        │               │
        │       ┌───────┴───────┐
        │       │               │
        │   CORRECT          WRONG
        │       │               │
        │       ▼               ▼
        │   BUG IS IN        BUG IS IN
        │   CONTROLLER       QUERY or DATA
        │   (Layer 5)        (Layers 6-7)
        │       │               │
        │       │               ▼
        │       │    Remove JOINs one by one
        │       │    → which JOIN breaks it?
        │       │    Check raw table:
        │       │    SELECT * FROM {table} WHERE {id}
        │       │    → Data wrong in DB?
        │       │    → WHO wrote it? log_detail
        │       │    → WHEN? timestamps
        │       │    → WHAT operation? Save/Edit/Cancel
        │       │
        ▼       ▼
   ┌────────────────────┐
   │  ROOT CAUSE FOUND  │
   │  → Hand to FIX     │
   └────────────────────┘
```

**Target: Root cause in ≤ 3 bisect steps. If it takes more, reassess your hypothesis.**

---

## Smell Tests (Instant Pattern Recognition)

Before running the full binary search, check if the bug SMELLS like a known pattern:

| Smell | Instant Diagnosis | Skip To |
|-------|-------------------|---------|
| "Works in Add, fails in Edit" | Edit uses different JS/PHP branch, or missing hidden field | Check JS: `if (mode == 'edit')` branch |
| "Total is wrong on report" | Footer callback column index mismatch (PAT-DT-001) | Check DataTable `footerCallback` column index |
| "Report ≠ screen data" | Different SQL queries or different date columns | Diff both model methods' SQL |
| "Was working yesterday" | Recent code or config change | `git log -5 --oneline -- {file}` |
| "Only one client affected" | Client config divergence or fingerprint drift | Diff client vs source code |
| "After cancel, data wrong" | Cancel didn't reverse all tables | `cancel_corruption.md` playbook |
| "Button does nothing" | JS error blocking execution | F12 → Console → red errors |
| "White screen / 500" | PHP parse error | Apache log, NOT CI log. `php -l {file}` |
| "Works on dev, fails on prod" | Case sensitivity (Linux), config diff, cache | Check filename case + `database.php` |
| "Intermittent / random" | Race condition, session expiry, or token expiry | Check for concurrent AJAX or session timeout |
| "Slow page / timeout" | N+1 query or missing index | `performance.md` playbook |
| "Wrong dropdown options" | AJAX endpoint returns wrong WHERE filter | `dropdown_data.md` playbook |

**If a smell matches → jump directly to the diagnosis. Don't run the full protocol.**

---

## Compare With Working (Copy-Paste Codebase Technique)

When two similar modules exist and one works but the other doesn't:

```
1. Identify the WORKING equivalent:
   - Billing save works → Purchase save doesn't?
   - Sales report correct → Purchase report wrong?
   - Module A's cancel works → Module B's cancel doesn't?

2. Find both implementations:
   grep -rn "function save_bill" admin/application/models/ --include="*.php"

3. DIFF them:
   - Side-by-side comparison of the two model methods
   - The DIFFERENCE between working and broken IS the bug

4. Common finds:
   - Copy-pasted code with wrong variable name ($bill_type not updated)
   - Missing WHERE clause that was in the original
   - Wrong table name (ret_billing vs ret_purchase)
   - Opposite stock logic (add vs subtract)
```

**This technique skips the entire binary search. Use it whenever a working reference exists.**

---

## Time-Boxing Protocol

| Time | Status | Action |
|------|--------|--------|
| **0-10 min** | Binary search | Bisect should identify the broken layer |
| **10-20 min** | Root cause | Should have exact file + line + what's wrong |
| **20-30 min** | Stuck? | STOP. Reassess: is your hypothesis wrong? Try a completely different angle |
| **30-45 min** | Still stuck? | Escalate: ask the human for more context. Check if the bug is even what you think it is |
| **45+ min** | Red flag | You're on the wrong track. Go back to Step 1 with fresh eyes. Consider: is the reported symptom actually the real problem? |

**Rule**: If you've been reading code for 15 minutes without checking data, you're violating Law #1. Stop and check the data.

---

## Log Injection Protocol (When Bisect Is Unclear)

When the binary search doesn't give a clear answer (e.g., response is partially correct):

```php
// TEMPORARY — inject at strategic bisect points, remove after debugging

// In Controller — after model call:
$result = $this->billing_model->get_bill_data($id);
log_message('debug', 'BISECT-CTRL: ' . json_encode($result));

// In Model — after query:
$query = $this->db->get('ret_billing');
log_message('debug', 'BISECT-MODEL: ' . $this->db->last_query());
log_message('debug', 'BISECT-ROWS: ' . $query->num_rows());

// Check log:
// tail -20 admin/application/logs/log-{YYYY-MM-DD}.php | grep BISECT
```

**⚠️ CLEANUP CHECKLIST** — after debugging, remove ALL injected log lines:
```bash
grep -rn "BISECT-" admin/application/ --include="*.php"
# → Remove every line found
```

---

## The Debug Handshake (AI ↔ Human)

Debugging requires runtime data AI can't access. Use structured rounds:

### Round 1: Symptom Collection
```
AI asks (ALL of these, no exceptions):
1. What page/screen/module? (exact URL or menu path)
2. What is WRONG? (exact wrong value or behavior)
3. What is EXPECTED? (exact correct value)
4. Is it ALL records or specific ones? (give bill number / ID)
5. When did it start? (always / recently / after an update)
6. Does it work on a DIFFERENT page/module? (for compare-with-working)

Human provides: answers + screenshot if available
```

### Round 2: Binary Search (AI works alone + asks for data)
```
AI does:
1. RUN SMELL TESTS — does this match a known pattern?
   → If match → skip to targeted investigation
2. BISECT — ask human for AJAX response data (Step 1 of protocol)
3. Based on response correctness → trace into backend OR frontend
4. Generate targeted SQL queries for the human to run

AI outputs: "The bug is in {FRONTEND/BACKEND}. Run these queries:"
```

### Round 3: Data Analysis + Root Cause
```
Human: [pastes query results / console errors]
AI:
1. Compare actual vs expected at the bisect point
2. Narrow to exact file, function, line
3. If STILL ambiguous → one more bisect (inject log, ask for log output)
4. Announce root cause with evidence
```

### Round 4 (if needed): Confirmation
```
AI outputs:
- Root cause: exact file, function, line, what's wrong
- Evidence: which data check proved it
- Matches recipe? → link to recipe
- New bug? → classify severity, create intake entry
- Hand off to ARCHITECT role (Layer 1)
```

**Target: 2-3 rounds. Time: 10-20 min. Max 4 rounds.**

---

## CI Framework Debug Toolkit

Quick reference — detailed procedures in `ci_framework_debugging.md` playbook.

| Need | Command / Technique |
|------|-------------------|
| See actual SQL | `echo $this->db->last_query(); die;` (after query) |
| Full request profiling | `$this->output->enable_profiler(TRUE);` (in controller) |
| Last DB error | `$this->db->error()` (after failed query) |
| Targeted logging | `log_message('debug', json_encode($var));` |
| PHP syntax check | `php -l admin/application/controllers/{file}.php` |
| Today's error log | `tail -50 admin/application/logs/log-{YYYY-MM-DD}.php` |
| Apache error log (XAMPP) | `type C:\xampp\apache\logs\error.log` (Windows) |
| Check POST data | `log_message('debug', json_encode($this->input->post()));` |
| Git recent changes | `git log -10 --oneline -- {file}` |
| Table structure | `SHOW CREATE TABLE {table}\G` |

---

## Symptom → Playbook Router

| # | Symptom | Playbook | Start Layer |
|---|---------|----------|-------------|
| 1 | Page shows wrong data | `wrong_data.md` | Layer 3 (response) |
| 2 | Action fails silently | `silent_failure.md` | Layer 2 (JS console) |
| 3 | Action gives error | `action_error.md` | Layer 5 (error log) |
| 4 | Feature stopped working | `regression.md` | Git log first |
| 5 | Wrong dropdown data | `dropdown_data.md` | Layer 3 (AJAX response) |
| 6 | Permission denied | `permission.md` | Layer 5 (session/RBAC) |
| 7 | Data mismatch between modules | `data_mismatch.md` | Layer 6 (diff both SQLs) |
| 8 | Duplicate records | `duplicates.md` | Layer 7 (DB constraints) |
| 9 | Data corruption after cancel | `cancel_corruption.md` | `_cancel_master.md` |
| 10 | Performance issue | `performance.md` | Layer 6 (EXPLAIN) |
| 11 | SMS/notification not sent | `notification.md` | Layer 5 (API response) |
| 12 | Client-specific bug | `client_specific.md` | Fingerprint diff |
| 13 | CI framework issue | `ci_framework_debugging.md` | Layer 4 (routing) |
| 14 | JS/frontend bug | `js_debugging.md` | Layer 2 (console) |
| 15 | CSS/print layout bug | `css_print_debugging.md` | Layer 1 (visual) |

---

## Anti-Patterns (What NOT To Do)

- ❌ **Don't read 500 lines of code before checking the data** — violates Law #1
- ❌ **Don't assume the bug is in the file the user mentions** — the symptom file is often not the cause file
- ❌ **Don't fix the first suspicious thing you find** — prove it's THE cause, not A cause
- ❌ **Don't change CSS by trial-and-error** — find a working reference first (see `css_print_debugging.md`)
- ❌ **Don't skip the smell test** — 50% of bugs match a known pattern
- ❌ **Don't debug for 30+ minutes without checking data** — you're reading code, not debugging
- ❌ **Don't propose a fix without reproducing the bug** — violates Law #5

---

## Environment Bridge

AI can't access runtime. Here's exactly what to ask the human for:

| Need | Human Command (Windows/XAMPP) | What To Paste |
|------|------|------|
| PHP error log | `type admin\application\logs\log-{date}.php \| findstr ERROR` | Error lines |
| Browser JS error | F12 → Console tab | Red error messages |
| AJAX response | F12 → Network → click request → Response | Response body |
| DB query result | Run SQL in phpMyAdmin | Copy result |
| Git recent changes | `git log -10 --oneline -- {file}` | Commit list |
| PHP version | `php -v` | Version string |
| Apache error log | `type C:\xampp\apache\logs\error.log` | Last 20 lines |
| Table structure | `SHOW CREATE TABLE {table}\G` in phpMyAdmin | DDL output |
| POST data received | Add `log_message('debug', json_encode($this->input->post()));` | Log output |
| Session data | Add `log_message('debug', json_encode($this->session->userdata()));` | Log output |
