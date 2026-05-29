# 🗺️ Complete Workflow Walkthrough — Estimation Module

> This document walks through every workflow in the Bug Remediation System using the **Estimation module** as a live example. Each section shows the exact inputs, outputs, and real bug IDs from the Estimation audit.
>
> **System Version**: v1.5 | **Last Updated**: 2026-03-02 | **Workflows**: 16 total

---

## 📖 How to Read This Document

Each workflow section follows the same structure:
1. **When to use** — the trigger
2. **What you say** — the slash command
3. **What happens** — step-by-step with Estimation examples
4. **What comes out** — the output artifact

The workflows are presented in the order you'd use them for a brand-new module.

> **New in v1.5**: Added documentation for direct client-reported bug triage (without prior system audit). Clarified that Docker is NOT required for the GitHub MCP server — native Node.js with a PAT is sufficient.
>
> **New in v1.4**: Fixed step numbering to match actual workflow files, added missing steps (Coverage Scan, Constructor Analysis, Pre-Flight Checks, KB Gap Analysis), added 2 previously undocumented workflows (`/setup-new-client`, `/setup-existing-client`), corrected PHP path defaults, fixed rollback timing.
>
> **New in v1.3**: Workflows now reference `.agent/config.md` for all environment paths, track bugs in `ACTIVE_BUGS.md`, log rollbacks in `ROLLBACK_REGISTRY.md`, and capture fix velocity metrics.

---

## Workflow 1: `/build-module-brain`

> **Trigger**: First time working with a module — ALWAYS do this before any audit.

### What You Say
```
/build-module-brain
Module: Estimation
```

### What Happens (Estimation Example)

**Step 0 — Existing Brain Detection** *(v1.2+)*:
Antigravity checks if `knowledge_brain/Estimation/MODULE_BRAIN.md` already exists:
- **Not found** → Proceed with full build (first time)
- **Found** → Offers 3 modes:
  - **Refresh** — merge new findings into existing brain
  - **Upgrade** — diff-based update for version changes
  - **Force Rebuild** — backup existing brain, build from scratch

**Steps 1–9**: Antigravity reads and analyzes the module:

| Step | What It Does | Estimation Files / Output |
|---|---|---|
| Step 1: Project Skeleton | List ALL files, sizes, connection flow | Controller, model, JS, views, config |
| Step 1.5: Constructor Analysis | Document loaded models, libraries, session gate | `$this->load->model()` calls, access checks |
| Step 2: Entry Points & Routes | Build route table of ALL public methods | URL → controller method → view mapping |
| Step 3: Engine Reverse Engineering | Trace CREATE, EDIT, DELETE flows end-to-end | JS → AJAX → Controller → Model → DB → View |
| Step 4: Business Rules Extraction | Extract every calculation/constraint as `RULE-EST-NNN` | Formulas, validation locations, edge cases |
| Step 5: Cross-Module Mapping | Map dependencies on external modules | Inventory, Accounts, Tagging reads/writes |
| Step 6: DB Truth Protocol | Create verification SQL queries | Diagnostic queries for orphan records, integrity |
| Step 6b: INVARIANT_MATRIX | Build variant × behavior grids (if config-driven) | Scheme Type, Tax Type, Discount Type |
| Step 6c: FORENSIC_TEMPLATE | Layer-by-layer investigation cheat sheet + module-specific layers | Base layers 1–6 + specialized layers |
| Step 7: Build METHOD_INDEX | Alphabetical method lookup with tables & callers | Controller + model methods, JS→Controller AJAX map, table reverse map |
| Step 8: Assemble Module Brain | Create MODULE_BRAIN.md with architecture overview | Overview, routes, key tables, risks, anti-patterns register |
| Step 9: Coverage Progress Scan (**MANDATORY**) | Measure % of module documented, track improvement | Weighted coverage across methods, tables, views, flows |

**Step 6c detail** — FORENSIC_TEMPLATE includes module-specific investigation layers *(v1.3+)*:
```
Base Layers: 1–6 (standard investigation flow)
Module-Specific Layer 7a: Tax Calculation Trace (Estimation has GST logic)
Module-Specific Layer 7b: Variant Isolation (Estimation has INVARIANT_MATRIX)
```

> Antigravity picks 2-3 specialized layers based on what the brain discovered — not all 7 options.

### What Comes Out
```
knowledge_brain/Estimation/
├── MODULE_BRAIN.md          (30 KB — architecture, routes, risks)
├── METHOD_INDEX.md          (27 KB — every model method with table mappings)
├── DATA_FLOW.md             (20 KB — data flow traces + JS function map)
├── BUSINESS_RULES.md        (15 KB — extracted business rules and formulas)
├── CROSS_MODULE_MAP.md      (13 KB — dependencies on Billing, Stock, etc.)
├── SCHEMA_ANALYSIS.md       (30 KB — all 10+ tables analyzed)
├── INVARIANT_MATRIX.md      (optional — variant × behavior grids)
├── FORENSIC_TEMPLATE.md     (investigation cheat sheet + module-specific layers)
└── COVERAGE_TRACKER.md      (round-by-round coverage progress — MANDATORY)
```

### ➡️ Next: `/module-bug-audit`

---

## Workflow 2: `/module-bug-audit`

> **Trigger**: Brain is built. Now find all the bugs.

### What You Say
```
/module-bug-audit
Module: Estimation
```

### What Happens (Estimation Example)

**Step 0 — Existing Audit Detection** *(v1.1+)*:
Antigravity checks if `bug_report_AI/Estimation/*CONSOLIDATED*` exists:
- **Not found** → Full audit (first time)
- **Found** → Offers 2 modes:
  - **Incremental** — preserve `✅ Fixed` entries, find new bugs, detect regressions
  - **Full Re-Audit** — backup existing data, rebuild from scratch

**Step 1 — Pre-Flight Checks**: Load patterns library, module brain, and METHOD_INDEX. If brain doesn't exist → STOP → "Run `/build-module-brain` first."

**Step 2 (Round 0) — Pattern Pre-Scan**: Antigravity reads `COMMON_BUG_PATTERNS.md` and greps for every detection rule:
```
PAT-SEC-001 (SQL Injection)     → grep '$searchField' → FOUND in 10+ methods
PAT-TXN-001 (trans_commit bug)  → FOUND in save functions
PAT-QRY-001 (Cartesian JOIN)    → FOUND in 2 methods
PAT-VAR-001 (Copy-paste error)  → FOUND in 3 methods
... 17 patterns scanned, 14 matched
```

**Step 3 (Round 1) — Controller Analysis**: Read `admin_ret_estimation.php`, check routes, permissions, input validation
**Step 4 (Round 2) — DB/Schema**: Run `SCHEMA_ANALYSIS.md` checks, find missing AUTO_INCREMENT, wrong column types
**Step 5 (Round 3) — Model**: Read `ret_estimation_model.php`, find query bugs, variable typos
**Step 6 (Round 4) — JS/Views**: Read `ret_estimation.js`, find always-true conditions, validation overwrites
**Step 7 (Round 5) — Calculations**: Compare JS vs PHP formulas for each field
**Step 8 (Round 6) — AJAX/Integration**: Check all AJAX endpoints for error handling

**Step 9 — Track A/B Classification**: Assign every bug a Track (A = System, B = Business) based on whether the code runs but produces wrong business results (Track B) or has a code defect (Track A).

**Step 10 — Consolidation**: Create consolidated report + execution plan with 11 sections per bug.

**Step 11 — KB Gap Analysis**: Compare bugs found against Module Brain coverage, identify undocumented methods/tables/flows, write gaps to `KB_GAP_ANALYSIS.md`.

**Step 12 — Post-Audit Pattern Library Update**: Add module references to existing patterns, create new pattern entries for novel bugs.

### What Comes Out
```
bug_report_AI/Estimation/
├── ROUND_1_CONTROLLER_ANALYSIS.md          ← Round 1 findings
├── ROUND_2_SCHEMA_ANALYSIS.md              ← Round 2 findings
├── ROUND_3_DEEP_DIVE.md                    ← Round 3 findings
├── ROUND_4_JS_VIEW_DEEP_DIVE.md            ← Round 4 findings
├── ROUND_5_JS_CALCULATIONS_DEEP_DIVE.md    ← Round 5 findings
├── ROUND_6_VALIDATION_AJAX_DEEP_DIVE.md    ← Round 6 findings
├── ESTIMATION_CONSOLIDATED_BUG_REPORT.md   ← Master list of all 65 bugs found
├── ESTIMATION_BUG_FIX_EXECUTION_PLAN.md    ← Detailed fix plan per bug
└── KB_GAP_ANALYSIS.md                      ← Brain coverage gaps found during audit
```

**Estimation Result**: 65 bugs found across 6 rounds:
| Severity | Count | Example Bug IDs |
|---|---|---|
| P0 (Critical) | 8 | EST-S01 (missing AUTO_INCREMENT), EST-R301 (SQL injection) |
| P1 (High) | 25 | EST-R302 (Cartesian JOIN), EST-R601 (trans_commit on failure) |
| P2 (Medium) | 22 | EST-R309 (missing scope filter), EST-S03 (int column for decimal) |
| P3 (Low) | 10 | EST-R607 (mkdir 0777) |

### ➡️ Next: `/bug-intake-triage` (for each bug)

---

## Workflow 3: `/bug-intake-triage`

> **Trigger**: A bug is discovered (from audit, client report, QA, or production alert).
>
> **Important**: This workflow is a **standalone entry point**. It does NOT require `/module-bug-audit` to have been run first. You can triage a client-reported bug directly.

### What You Say
```
/bug-intake-triage
Bug: EST-R301 — SQL Injection via column name in DataTables search
```

### What Happens (Estimation Example — EST-R301)

**Step 1 — Collect Bug Report Fields**: Gather required fields — reporter, module, sub-module, steps to reproduce, expected/actual behavior, evidence, environment, frequency. If any required field is missing, ask the reporter.

**Step 2 — Assign Severity**: Classify using P0–P3 criteria. Decision rule: if the bug causes money to be wrong or data to be lost → P0. If it shows wrong info → P1.

**Step 3 — Assign Track**: Track A (System) if code defect. Track B (Business) if code runs but produces wrong business results.

**Step 4 — Assign Category**: Pick ONE primary category from 8 options (Logic, Data Validation, Database, Exception Handling, Security, Performance, Integration, Concurrency).

**Step 5 — Generate Bug ID**: Format: `{PREFIX}-{SOURCE}{SEQ}`. Source codes: `R1`–`R6` (audit rounds), `PAT` (pattern match), `CLT` (client), `QA` (QA), `INT` (internal).

**Step 5b — Duplicate Bug Check** *(v1.2+)*:
Before creating a new entry, Antigravity searches for duplicates:
1. Search local `ESTIMATION_CONSOLIDATED_BUG_REPORT.md` for matching file + method
2. Search GitHub Issues: `github-mcp-server → search_issues` for similar title/description
3. If duplicate found → WARN with link to existing bug, ask to merge or skip

**Step 6 — Quick Pattern Check**: Read `COMMON_BUG_PATTERNS.md` — match found → fix template available; no match → novel bug.

**Step 7 — Assign Sprint**: Sprint 1 = P0 + critical P1, Sprint 2 = remaining P1 + high P2, Sprint 3 = remaining P2 + all P3.

**Step 8 — Check Module Brain Readiness**: Verify `knowledge_brain/Estimation/MODULE_BRAIN.md` exists. If not → flag for brain build.

**Step 9 — Create Bug Entry**: Write structured bug entry to `bug_report_AI/Estimation/`.

| Field | Value |
|---|---|
| **Bug ID** | EST-R301 |
| **Title** | Column Name SQL Injection in DataTables Server-Side |
| **Severity** | P0 (Critical — Security) |
| **Track** | **A (System)** — This is a code defect, not a business logic issue |
| **Category** | Security |
| **Pattern Match** | ✅ PAT-SEC-001 — known pattern with fix template |
| **Module Brain Ready** | ✅ `knowledge_brain/Estimation/` exists |
| **Sprint** | Sprint 1 (P0 = immediate) |

**Step 10 — Send Acknowledgement**: Notify reporter per SLA (Client ≤ 2h, Support ≤ 4h, QA ≤ 1 day, AI Audit = same sprint).

**GitHub Issue Created** *(via `/github-bug-tracking` Part 2)*:
```
✅ GitHub Issue #42 created
   Title: [EST-R301] Column Name SQL Injection in DataTables Server-Side
   Labels: P0-critical, Track-A-system, cat:security, module:Estimation, status:triaged
   Milestone: Sprint 1
   Assignee: kanaga-sundar
```

### What Comes Out
```
Bug Entry:
  EST-R301 | P0 | Track A | Security | Sprint 1
  Pattern: PAT-SEC-001 (Column Name SQL Injection)
  GitHub: #42
  → Route to: /fix-single-bug
```

### Client-Reported Bug — Direct Triage (No Audit Required)

> **Use this path** when a client reports a bug directly and you want to fix it immediately — without running a full `/module-bug-audit` first.

**Prerequisites** (all that's needed):
- Module name is known
- `bug_report_AI/COMMON_BUG_PATTERNS.md` exists (run `/manage-bug-patterns` → Init if it doesn't)

**What You Say**:
```
/bug-intake-triage
Source: Client
Module: Billing
Bug: Customer says invoice total is wrong when applying a 5% discount on a multi-item bill
```

**How It Differs from Audit-Sourced Bugs**:

| Aspect | Audit-Sourced Bug | Client-Reported Bug |
|---|---|---|
| Bug ID source code | `R1`–`R6` (audit round) | `CLT` (client) |
| Example ID | `EST-R301` | `BIL-CLT01` |
| Prerequisite | Audit completed | None (just pattern library) |
| Acknowledgement SLA | Same sprint | ≤ 2 hours |
| Duplicate check | Against consolidated audit report | Against GitHub Issues + any existing local reports |
| Module Brain | Should exist (audit requires it) | Checked in Step 8 — flagged if missing, but triage still completes |

**The full triage flow is identical** — Steps 1 through 10 run the same way. The only differences are the source code in the bug ID and the SLA for acknowledgement.

**Complete flow for a direct client bug**:
```
Client reports bug
  → /bug-intake-triage (Source: Client)
  → Bug ID: BIL-CLT01 (CLT source code)
  → GitHub Issue #N created
  → Acknowledgement sent to client (≤ 2 hours)
  → /fix-single-bug BIL-CLT01
  → /fix-architecture-bug or /fix-business-bug (based on track)
  → /learn-and-improve BIL-CLT01
  → Done
```

> **Note**: If the Module Brain doesn't exist yet, triage will flag it at Step 8 but still complete. You'll need to run `/build-module-brain` before starting the fix workflow.

### ➡️ Next: `/fix-single-bug`

---

## Workflow 4: `/fix-single-bug`

> **Trigger**: Bug triaged, ready to fix.

### What You Say
```
/fix-single-bug EST-R301
```

### What Happens — Pre-Fix Guards *(v1.2+)*

**Step 0 — Duplicate Fix Guard**:
Checks if EST-R301 is already `✅ Fixed` or `🔧 In Progress` in the execution plan or GitHub → Not fixed → proceed.

**Step 0b — Register in Active Bug Tracker** *(v1.3+)*:
Adds row to `bug_report_AI/ACTIVE_BUGS.md`:
```
| EST-R301 | Estimation | P0 | A | Step 1: DIAGNOSE | /fix-single-bug | kanaga-sundar | 2026-02-19 14:00 | #42 |
```

**Step 0c — Dependency Check** *(v1.3+)*:
Reads `ACTIVE_BUGS.md` → checks if any other in-progress bugs touch `ret_estimation_model.php`:
```
No other in-progress bugs touching this file → Proceed normally
```

### What Happens — The Routing Decision

**Step 1 (DIAGNOSE)**: Read execution plan, check brain, match pattern. Cross-reference `BUSINESS_RULES.md`, `DATA_FLOW.md`, `CROSS_MODULE_MAP.md`, `METHOD_INDEX.md`.
**Step 2 (LOCATE)**: Find affected file and code. Update GitHub Issue: `status:triaged` → `status:in-progress`.
**Step 3 (ASSESS)**: Determine track, risk level, and cross-module impact.

For EST-R301:
```
Track: A (System) — SQL Injection is a code defect
Complexity: HIGH — affects 10+ methods
→ ROUTE TO: /fix-architecture-bug
```

For a business bug like a calculation error:
```
Track: B (Business) — wrong formula is a business logic issue
→ ROUTE TO: /fix-business-bug
```

For a simple bug (e.g., EST-R607 mkdir 0777):
```
Track: A (System) — code defect
Complexity: LOW — single line change
→ CONTINUE with Steps 4–8 in fix-single-bug (no need for dedicated sub-workflow)
```

### What Happens — Steps 4–8 (Simple Fix Path)

**Step 4 — PLAN & APPLY FIX**: Document rollback plan **BEFORE** applying the fix. Then apply minimal fix using layer-specific sub-procedures:

| Layer | Sub-Procedure |
|---|---|
| PHP Controller / Model | Locate, confirm, adapt pattern template or use execution plan fix |
| DB Schema | Generate `ALTER TABLE` + rollback SQL, **DO NOT EXECUTE** — human executes |
| JavaScript | Surgical precision, verify counterpart functions, flag for manual cache clear |
| View / Template | Fix XSS, duplicates, test both Add/Edit modes |

**Step 4a — Rollback Registry** *(v1.3+)*:
After applying the fix, append rollback entry to `bug_report_AI/ROLLBACK_REGISTRY.md`:
```
| EST-R301 | Estimation | ret_estimation_model.php | Remove whitelist arrays from 10 methods | Low — restores original (vulnerable) behavior | 2026-02-19 |
```

**Step 5 — SYNTAX CHECK**: `& "{PHP_PATH}" -l {file}` → No errors
**Step 6 — TEST**: Run via `/test-and-verify` — syntax check, category-specific tests, smoke test checklist.
**Step 7 — APPROVAL GATE**: Present diff + test results to human → Approve / Modify / Reject.
**Step 8 — CLOSE & UPDATE**: Mark bug fixed, update pattern library, add anti-pattern to brain, generate fix walkthrough, notify reporter.

### ➡️ Next: `/fix-architecture-bug` (Track A) or `/fix-business-bug` (Track B)

---

## Workflow 5a: `/fix-architecture-bug` (Track A)

> **Trigger**: `/fix-single-bug` Step 3 routes here for complex system/architecture bugs.

### What You Say
```
/fix-architecture-bug EST-R301
```

### What Happens (EST-R301 — SQL Injection)

**Step 0 — Module Brain Check** *(v1.1+)*:
Checks `knowledge_brain/Estimation/MODULE_BRAIN.md` → ✅ Exists → Load full brain context.

**Step 0b — Load Central Config** *(v1.3+)*:
Reads `.agent/config.md` → loads `{PHP_PATH}`, `{REPO_OWNER}`, `{REPO_NAME}`, module paths.

**Step 1 — DIAGNOSE**: Reads brain, matches PAT-SEC-001, uses fix template
**Step 2 — TRACE DATA FLOW**:
```
JS Layer      → DataTables sends column index, JS maps to column name
Controller    → Receives $searchField from POST — passes directly to model
Model Layer   → $this->db->like($searchField, $searchValue) — VULNERABLE
DB Layer      → Column name is user-controlled — injection possible
```

**Step 3 — CROSS-MODULE IMPACT**: Check CROSS_MODULE_MAP.md:
```
Estimation DataTables → used only within Estimation module
Cross-module impact: NONE
```

**Step 4 — PROPOSE FIX** (presented to human):
```markdown
### Fix Proposal for EST-R301

**Root Cause:** User input used as column name in SQL query.
Query bindings do NOT protect column names.

**Proposed Fix:**
- Add column name whitelist in 10+ model methods
- File: admin/application/models/ret_estimation_model.php
- Risk: LOW (whitelist is additive, doesn't change existing behavior)

**Alternative Approaches Considered:**
1. Parameterized column mapping array — rejected (heavier refactor)
2. Central middleware filter — rejected (CodeIgniter 3 architecture doesn't support it cleanly)

**Rollback Plan:** Remove whitelist array + if-check, revert to original line
```

**⏸️ Step 5 — HUMAN APPROVAL**: Human reviews the approach → APPROVE

**Step 6 — APPLY FIX** (Sub-Procedure G: Security):
```php
// BEFORE (in each affected method):
$this->db->like($searchField, $searchValue);

// AFTER:
$allowed_columns = ['est_no', 'customer_name', 'mobile', 'date'];
if (!in_array($searchField, $allowed_columns, true)) {
    $searchField = 'est_no';
}
$this->db->like($searchField, $searchValue);
```

> After applying, rollback entry is appended to `bug_report_AI/ROLLBACK_REGISTRY.md` *(v1.3+)*

**Step 7 — SYNTAX CHECK**: `& "{PHP_PATH}" -l ret_estimation_model.php` → No errors
**Step 8 — TESTS**: Unit test verifying disallowed columns are blocked
**Step 9 — DOCUMENT**: Fix walkthrough written

**⏸️ Step 10 — HUMAN REVIEW**: Human reviews diff → APPROVE

**GitHub Issue Updated**:
```
Issue #42: status:triaged → status:in-progress → status:testing
Comment: "🔧 Fix applied — column whitelist added to 10 methods. Running tests."
```

### What Comes Out
```
✅ Bug EST-R301 — SQL Injection via Column Name (Track A: Architecture)
   Category: Security
   Fix: Added column name whitelist to 10 model methods
   File: admin/application/models/ret_estimation_model.php
   Tests: 2 tests, 4 assertions — all passed
   Pattern: PAT-SEC-001 matched
   Rollback: Logged in ROLLBACK_REGISTRY.md
   Next: /learn-and-improve → /fix-single-bug EST-R302
```

### ➡️ Next: `/learn-and-improve`

---

## Workflow 5b: `/fix-business-bug` (Track B)

> **Trigger**: `/fix-single-bug` Step 3 routes here for business logic bugs.

### Hypothetical Example: EST-B001 — Wrong Discount Calculation

Let's say an Estimation bug is found where the discount amount is calculated incorrectly when scheme type is "Diamond" and discount type is "per-gram":

### What You Say
```
/fix-business-bug EST-B001
```

### What Happens

**Step 0 — Module Brain Check** *(v1.1+)*:
Checks brain → ✅ Exists → loads `BUSINESS_RULES.md`, `INVARIANT_MATRIX.md`, `SCHEMA_ANALYSIS.md`.

> If brain was missing, displays HIGH RISK warning: "Business bug diagnosis WITHOUT brain is HIGH RISK. STRONGLY RECOMMENDED: Run `/build-module-brain` first."

**Step 0b — Load Central Config** *(v1.3+)*:
Reads `.agent/config.md` → loads env paths + repo info.

**Step 1 — IDENTIFY VIOLATED RULE**: Read `BUSINESS_RULES.md`:
```
RULE-EST-015: Discount per gram = discount_rate × net_weight
Currently computing: discount_rate × gross_weight ← WRONG
```

**Step 2 — DB TRUTH PROTOCOL**: Run diagnostic SQL:
```sql
SELECT est_id, gross_wt, net_wt, discount_rate,
       (discount_rate * net_wt) AS expected_discount,
       discount_amount AS actual_discount,
       (discount_rate * net_wt) - discount_amount AS delta
FROM ret_estimation_details
WHERE discount_type = 'per_gram' AND discount_amount > 0
LIMIT 10;
```
Result: Delta is non-zero for all per-gram discounts → confirmed bug

**Step 3 — LAYER-BY-LAYER TRACE**:
```
JS Layer:      discount = parseFloat(rate) * parseFloat(gross_wt)  → VALUE: 150.00
Controller:    Receives 150.00 from POST
Model:         Stores 150.00 in discount_amount column
DB:            Stores 150.00
Expected:      rate × net_wt = 120.00
FAULT LAYER:   JS — using gross_wt instead of net_wt
```

**Step 4 — PRESENT FINDINGS**: Antigravity presents analysis with formula comparison, fault location, proposed fix, risk assessment, and variant-specific checks.

**⏸️ Step 5 — HUMAN VALIDATION**:
> "Is the correct formula: `discount_rate × net_weight`?"
> Human: **"Yes, confirmed — discount per gram uses net weight."**

**Step 6 — APPLY FIX** (Sub-Procedure A: Calculation Error):
Fix in BOTH JS and PHP:
```javascript
// JS — ret_estimation.js
// BEFORE: var discount = parseFloat(rate) * parseFloat(gross_wt);
// AFTER:  var discount = parseFloat(rate) * parseFloat(net_wt);
```
```php
// PHP — ret_estimation_model.php
// BEFORE: $discount = $row['discount_rate'] * $row['gross_wt'];
// AFTER:  $discount = $row['discount_rate'] * $row['net_wt'];
```

> Full sub-procedures available in the workflow: A (Calculation Error), B (Missing Business Rule), C (Configuration Bug), D (Incorrect Workflow), E (Cross-Module Logic).

> Rollback entry appended to `bug_report_AI/ROLLBACK_REGISTRY.md` *(v1.3+)*

**Step 7 — SYNTAX CHECK**: `& "{PHP_PATH}" -l {file}` → No errors

**Step 8 — TESTS**: Test normal input + edge cases (0 net_wt, matching gross/net, etc.). Variant coverage via INVARIANT_MATRIX.

**Step 9 — ROLLBACK PLAN**: Document rollback steps before proceeding to human review. Note: rollback will re-introduce the incorrect calculation.

**⏸️ Step 10 — HUMAN SMOKE TEST**: Human creates a real estimation with per-gram discount, verifies amount matches `rate × net_weight`

**Post-Fix**: If the fix reveals a new business rule → add to `BUSINESS_RULES.md`. If INVARIANT_MATRIX variant behavior changed → update it.

### ➡️ Next: `/learn-and-improve`

---

## Workflow 6: `/test-and-verify`

> **Trigger**: Called automatically from within fix workflows, or standalone.

### What Happens (EST-R301 Example)

**Step 0 — Prerequisite Check** *(reads from `.agent/config.md`)*:
```
Step 0a: PHP CLI Check    → & "{PHP_PATH}" -v → auto-detect if not found
Step 0b: PHPUnit Version  → PHP 7.3–7.4 → PHPUnit 9.6 | PHP 8.1+ → PHPUnit 10.x
Step 0c: PHPUnit Check    → auto-install via Composer if missing
```

**Environment** *(from `.agent/config.md`)*:
```
PHP:      {PHP_PATH} → D:\xampp\php\php.exe (default, update per project)
PHPUnit:  {PHPUNIT_PATH} → admin/tests/vendor/bin/phpunit
Tests:    {TEST_DIR} → admin/tests/
```

**Step 1 — Syntax Check**: `& "{PHP_PATH}" -l ret_estimation_model.php` → ✅ No errors

**Step 2 — Identify Required Tests by Category**:
| Category | Required Tests |
|---|---|
| Security | Whitelist enforcement, malicious payload blocking |
| Transaction | Success commits, failure rollbacks |
| Logic/Calculation | Normal input, edge cases (0, negative, max), boundary values |
| Data Validation | Valid passes, invalid blocked, null handling |

**Step 3 — Check for Existing Tests**: Search `{TEST_DIR}` for module test files, run to establish baseline.

**Step 4 — Create New Tests** (P0/P1 bugs always get a test):
```php
public function test_sql_injection_column_whitelist() {
    $result = $this->model->get_estimation_list("'; DROP TABLE--", "test");
    // Should use default column, not the injected value
    $this->assertNotEmpty($result);
}
```

**Step 5 — Run All Tests**: `& "{PHP_PATH}" {PHPUNIT_PATH} --no-configuration EstimationTest.php --testdox`

**Step 6 — Module Smoke Test Checklist** (manual checks):
- [ ] Primary **create/save** action works
- [ ] Primary **edit/update** preserves ALL data (no field loss)
- [ ] Primary **delete** cleans up ALL child records
- [ ] **Search** returns correct results
- [ ] **Print/export** matches DB values
- [ ] **Negative test** — malicious input rejected

**Step 7 — Sign-Off Criteria**: All prerequisites verified, syntax passes, tests pass, smoke test done, rollback documented.

### What Comes Out
```
Tests: 2 passed, 4 assertions
Syntax: ✅ clean
Smoke: ✅ module loads, records display
```

---

## Workflow 7: `/learn-and-improve`

> **Trigger**: After every successful fix.

### What You Say
```
/learn-and-improve EST-R301
```

### What Happens (EST-R301 Example)

**Step 0 — Quick Impact Assessment** *(v1.2+)*:
| Change Type | EST-R301 | Action |
|---|---|---|
| Method signature/behavior changed? | No — only added whitelist guard | Skip METHOD_INDEX deep update |
| Data flow changed? | No | Skip DATA_FLOW update |
| Business rule corrected? | No | Skip BUSINESS_RULES update |
| Schema changed? | No | Skip SCHEMA_ANALYSIS update |

**Step 0b — Capture Fix Velocity** *(v1.3+)*:
```
Started: 2026-02-19 14:00 (from ACTIVE_BUGS.md)
Completed: 2026-02-19 15:30
Duration: 90 minutes
→ Appended to bug_report_AI/FIX_VELOCITY.md
```

**Step 1 — Update Module Brain**:
Target specific MODULE_BRAIN.md sections: §10 Bug Root Cause Register, §11 Code Anti-Patterns Map, §12 Function-Level Code Map, §13 Field-Level Trace, §15 Unit Test Derivation Map, §21 Gaps & Risks.

```markdown
### 1a. Anti-Pattern Entry:
Added to MODULE_BRAIN.md → Anti-Patterns Register:

### EST-R301: SQL Injection via Column Name ✅ FIXED

| Bug ID | Anti-Pattern | Fix Applied | Date |
|---|---|---|---|
| EST-R301 | User input used as SQL column name | Column whitelist + safe default | 2026-02-19 |

**Prevention**: Never use user input as column/table names. Always whitelist.
```

```markdown
### 1b. "Why It Was Done This Way" Note:
> If the fix reveals developer intent that wasn't obvious, document it.

### 1c. Field-Level Data Flow (if applicable):
| Field | DB Column | PHP Variable | JS Variable | HTML Element |
|---|---|---|---|---|
```

**Step 2 — Update Pattern Library**:
PAT-SEC-001 already exists → add "Estimation" to "Modules Found In" ✅ (already listed)

**Step 3 — Update METHOD_INDEX.md** (if applicable):
Note that 10 methods now have whitelist arrays

**Step 4 — Update Execution Plan**:
Mark EST-R301 as ✅ Fixed in `ESTIMATION_BUG_FIX_EXECUTION_PLAN.md`
Update: `1/65 bugs fixed`

**Step 4b — Generate Fix Report Entry**:
```markdown
### Fix: EST-R301 — SQL Injection via Column Name
- **Date:** 2026-02-19
- **Track:** A (System)
- **Category:** Security
- **Severity:** P0
- **Files Changed:** admin/application/models/ret_estimation_model.php (10 methods)
- **Root Cause:** User-controlled value used as SQL column name
- **Fix Applied:** Column name whitelist with safe default fallback
- **Tests:** 2 tests, 4 assertions — PASS
- **Pattern:** PAT-SEC-001 matched
- **Rollback:** Entry in ROLLBACK_REGISTRY.md
```

**Step 4d — Close GitHub Issue**:
```
Issue #42: CLOSED
Comment: "✅ Bug Fixed — column whitelist added to 10 methods"
Labels: removed status:testing, kept P0-critical, Track-A-system, cat:security, module:Estimation
```

**Step 4e — Update Active Bug Tracker** *(v1.3+)*:
Move EST-R301 from **In-Progress** to **Recently Completed** in `ACTIVE_BUGS.md`:
```
| EST-R301 | Estimation | P0 | Column whitelist added to 10 methods | 2026-02-19 | 90 min | #42 |
```

**Step 4f — Closure Checklist** *(updated v1.3)*:
- [x] Brain updated (anti-pattern entry, §10-§11)
- [x] Pattern library checked
- [x] METHOD_INDEX updated
- [x] Execution plan updated
- [x] Fix report generated
- [x] GitHub Issue #42 closed
- [x] Active Bug Tracker updated
- [x] Fix velocity captured (90 min)
- [x] Rollback registered
- [x] Bug is **CLOSED**

**Step 5 — Monthly Metrics Review** (run monthly or after sprint):
Track: Time to Acknowledge, Time to Fix, First-Fix Success Rate, Bugs per Module per Month, Pattern Library Reuse Rate.

**Step 6 — Brain Staleness Check**: For each module with a Brain, check if controller/model/JS files have been modified since Brain was last updated. If stale → flag for incremental brain refresh.

### ➡️ Next: `/fix-single-bug EST-R302` (next bug in sprint)

---

## Workflow 8: `/manage-bug-patterns`

> **Trigger**: Before first audit (init), or when a novel bug is found.

### Scenario A: Initialize Library (First Time)
```
/manage-bug-patterns
Action: Initialize
```
Creates `bug_report_AI/COMMON_BUG_PATTERNS.md` with 16 seed patterns across 3 levels (System, Architecture, Business).

> **Note** *(v1.1+)*: Seed IDs use the format `SYS-001`, `ARCH-001`, `BIZ-001`. Once the library is live, new patterns use the format `PAT-{CAT}-NNN` (e.g., `PAT-SEC-001`). The live library is the authoritative source.

### Scenario B: Scan a New Module
```
/manage-bug-patterns
Action: Scan
Module: Billing
```
Runs every detection rule against the Billing module's files:
```
PAT-SEC-001 → grep '$searchField' in ret_billing_model.php → FOUND at line 45, 89
PAT-TXN-001 → search trans_commit in admin_ret_billing.php → NOT FOUND
PAT-QRY-001 → check JOINs → FOUND at line 203
...
Result: 8/17 patterns matched in Billing module
```

### Scenario C: Add a New Pattern (Novel Bug)
After fixing a novel bug that doesn't match any existing pattern:
```
/manage-bug-patterns
Action: Add
Pattern: PAT-AJAX-001 — Missing Error Callback in $.ajax
```
Antigravity adds the pattern with detection rule, fix template, and first module.

---

## Workflow 9: `/github-bug-tracking`

> **Trigger**: Called automatically during triage, fixing, and closure. Also used standalone for initial setup.

### MCP Server Setup — Docker NOT Required *(v1.5)*

The `github-mcp-server` used by this workflow runs as a **native Node.js process** (or binary). Docker is NOT a dependency.

**What you need:**
- A GitHub **Personal Access Token (PAT)** with `repo` scope
- The MCP server configured in your IDE/agent settings (e.g., `.gemini/settings.json` or equivalent)
- Node.js (if using the npm package) or the standalone binary

**What you do NOT need:**
- ❌ Docker / Docker Compose
- ❌ Any containerized infrastructure
- ❌ Docker Desktop running

> Some MCP server documentation may list Docker as an *optional* deployment method. For this project's use case (a single developer managing GitHub Issues via API), the native process is simpler and sufficient.

### One-Time Setup (Part 1)
Creates labels (P0–P3 severity, Track A/B, categories, modules, status) and Sprint 1–4 milestones on GitHub.

### During Bug Lifecycle
| Event | Workflow That Triggers It | GitHub Action |
|---|---|---|
| Bug triaged | `/bug-intake-triage` | Issue created with labels + milestone |
| Fix starts | `/fix-single-bug` Step 2 | `status:triaged` → `status:in-progress` |
| Fix applied | `/fix-architecture-bug` or `/fix-business-bug` | `status:in-progress` → `status:testing` |
| Bug closed | `/learn-and-improve` Step 4d | Issue closed with fix summary comment |
| Deploying | After fix verified | `status:testing` → `status:deploying` |

### Multi-Client Deployment (Parts 5–6)
- **CLIENT_REGISTRY.md** — tracks all active client instances
- **CLIENT_DEPLOYMENT_MATRIX.md** — tracks which clients have received which fixes
- **Cherry-pick deployment** — deploy individual bug fixes to specific clients via `git cherry-pick`
- **Patch files** — for clients without direct git remote access
- **Sprint bundles** — deploy an entire sprint's fixes at once
- Commit message convention: `fix({module}): [{BUG_ID}] {description}`

### Offline Fallback *(v1.1+)*
If GitHub is unreachable, issues are queued locally in `bug_report_AI/PENDING_GITHUB_ISSUES.md` and synced on next connection.

---

## Workflow 10: `/sprint-status` *(NEW v1.3)*

> **Trigger**: Sprint standup, status reporting, or anytime you need "where are we?"

### What You Say
```
/sprint-status
```

### What Happens
1. Reads `.agent/config.md` for repo info
2. Queries GitHub Issues by milestone (Sprint 1–4), counts by status label
3. Reads `ACTIVE_BUGS.md` for in-progress/blocked bugs
4. Reads `FIX_VELOCITY.md` for timing data (if available)

### What Comes Out
```
Sprint 1 (P0 Critical):  8 total | 1 done | 2 in-progress | 0 blocked | 5 queued | 12%
Sprint 2 (P1 High):     25 total | 0 done | 0 in-progress | 0 blocked | 25 queued |  0%
Sprint 3 (P2 Medium):   22 total | 0 done | 0 in-progress | 0 blocked | 22 queued |  0%
Sprint 4 (P3 Low):      10 total | 0 done | 0 in-progress | 0 blocked | 10 queued |  0%

Active: EST-R301 (Step 6 of /fix-architecture-bug)
Blocked: none
Average fix time: 90 min (1 data point)
```

---

## Workflow 11: `/system-health` *(NEW v1.3)*

> **Trigger**: Before sprint planning, management reporting, or deciding which module to audit next.

### What You Say
```
/system-health
```

### What Happens
1. Scans all `bug_report_AI/{MODULE}/` directories for audit data
2. Reads `COMMON_BUG_PATTERNS.md` for cross-module pattern spread
3. Generates risk heatmap + systemic pattern analysis + category distribution

### What Comes Out (Estimation-Only Example)
```
Module Risk Heatmap:
| Module     | Total | P0 | P1 | P2 | P3 | Fixed | Open | Risk  |
| Estimation | 65    | 8  | 25 | 22 | 10 | 1     | 64   | 🔴    |
| Billing    | —     | —  | —  | —  | —  | —     | —    | ⬜    |
| Payment    | —     | —  | —  | —  | —  | —     | —    | ⬜    |

⬜ = Not yet audited
🔴 = Open P0 bugs remain

Recommendation: Audit Billing next — similar codebase, patterns likely to match.
```

---

## Workflow 12: `/validate-workflows` *(NEW v1.3)*

> **Trigger**: After modifying any workflow, before onboarding a new developer, or monthly health check.

### What You Say
```
/validate-workflows
```

### What It Checks
1. All 16 workflow files exist in `.agent/workflows/`
2. Frontmatter has `description`, `version`, `last_updated` — warns if stale (>90 days)
3. Cross-references between workflows are valid (e.g., `/fix-single-bug` references `/fix-architecture-bug`)
4. Hardcoded paths flagged — should reference `.agent/config.md` variables
5. Supporting files exist (`ACTIVE_BUGS.md`, `ROLLBACK_REGISTRY.md`, `COMMON_BUG_PATTERNS.md`, etc.)

---

## Workflow 13: `/setup-new-client`

> **Trigger**: Setting up the Bug Remediation System for a brand-new client project (no existing brain or audit data).

### What You Say
```
/setup-new-client
Client: Madurai-XYZ
```

### What Happens
1. Creates the project directory structure (`bug_report_AI/`, `knowledge_brain/`, `.agent/`)
2. Copies `.agent/config.md` template and prompts for client-specific values (PHP path, repo, project root)
3. Initializes `COMMON_BUG_PATTERNS.md` via `/manage-bug-patterns` (init)
4. Creates empty tracking files (`ACTIVE_BUGS.md`, `ROLLBACK_REGISTRY.md`)
5. Sets up GitHub tracking via `/github-bug-tracking` (Part 1)
6. Registers client in `CLIENT_REGISTRY.md`

### ➡️ Next: `/build-module-brain` for the first module

---

## Workflow 14: `/setup-existing-client`

> **Trigger**: Setting up the Bug Remediation System for an existing client running an older version of the source codebase.

### What You Say
```
/setup-existing-client
Client: Theni-NPR
Source Brain: knowledge_brain/Estimation/
```

### What Happens
1. Creates a **derived client brain** — copies the source brain as a baseline
2. Diffs the client's codebase against the source to identify divergence
3. Marks divergent methods/routes in the derived brain with `⚠️ DIVERGENT` flags
4. Updates `.agent/config.md` with client-specific paths
5. Runs `/manage-bug-patterns` scan against the client's codebase
6. Creates deployment tracking files, registers in `CLIENT_REGISTRY.md`

### ➡️ Next: `/module-bug-audit` on the client's divergent code

---

## Workflow 15: `/overall-bug-dashboard`

> **Trigger**: Management reporting, sprint reviews, weekly standups, or anytime you need the full project picture.

### What You Say
```
/overall-bug-dashboard
```

### What Happens

**Step 1 — Load Config**: Read `.agent/config.md` for repo info.

**Step 2 — Discover Modules**: Scan `bug_report_AI/` for audited modules.

**Step 3 — Collect Local Data**: Read consolidated reports + execution plans for every module. Extract bug ID, title, severity, track, category, sprint, status, GitHub issue number.

**Step 4 — Pull Live GitHub Status**: Query ALL open and closed issues via `github-mcp-server` → `list_issues`. For each issue extract:
- Status labels (`status:triaged`, `status:in-progress`, `status:testing`)
- **Assignee** → who is working on it
- **Closed By** → who fixed it
- **Closed At** → when it was fixed
- Reconcile GitHub vs local status (flag inconsistencies)

**Step 5 — Build Developer Attribution**: Group closed issues by `closed_by` username. Show bugs fixed per developer, by severity and track, with average fix time.

**Step 6 — Generate Dashboard**: Single report containing:
- Executive summary (total bugs, fixed, open, in-progress, blocked)
- Module breakdown with risk score (🔴/🟡/🟢)
- Sprint progress across all 4 sprints
- **Developer attribution table** (who fixed what, how fast)
- Active bugs with assignee and current step
- Blocked bugs with reason and days blocked
- Recently fixed (last 10) with who fixed them
- Fix velocity trend (weekly/all-time)
- Data inconsistencies between local and GitHub
- Unaudited modules with risk estimate

**Step 7 — Save Report**: Save to `bug_report_AI/DASHBOARD_REPORTS/bug_dashboard_{DATE}.md`

### What Comes Out (Estimation-Only Example)
```
📊 Overall Bug Dashboard — 2026-02-21

Modules: 1 audited (Estimation), 8 unaudited
Total bugs: 65 (1 fixed, 0 in progress, 64 queued)

Developer Attribution:
| Developer      | Fixed | P0 | P1 | P2 | P3 | Avg Time |
| @kanaga-sundar | 1     | 1  | 0  | 0  | 0  | 90 min   |

Sprint 1 (P0): 8 total | 1 done (12%)
Sprint 2 (P1): 25 total | 0 done (0%)

Report saved: bug_report_AI/DASHBOARD_REPORTS/bug_dashboard_2026-02-21.md
```

### How It Differs from Other Workflows
| Feature | `/sprint-status` | `/system-health` | `/overall-bug-dashboard` |
|---|---|---|---|
| Developer attribution | ❌ | ❌ | ✅ |
| Live GitHub sync | ✅ (basic) | ❌ | ✅ (full reconcile) |
| Fix velocity trends | Basic | ❌ | ✅ (weekly/all-time) |
| Data inconsistency check | ❌ | ❌ | ✅ |
| Module risk heatmap | ❌ | ✅ | ✅ |

---

## 🔄 The Complete Cycle (Summary)

```
┌──────────────────────────────────────────────────────────────────────┐
│                                                                      │
│   .agent/config.md          Central config (loaded by all workflows) │
│                                                                      │
│   /setup-new-client         Set up new client project (one-time)     │
│   /setup-existing-client    Set up existing client (one-time)        │
│                                                                      │
│   /build-module-brain       Build knowledge base (ONE TIME)          │
│          │                  + Constructor analysis                    │
│          │                  + Module-specific forensic layers         │
│          │                  + Coverage progress scan (MANDATORY)      │
│          ▼                                                           │
│   /manage-bug-patterns      Load/init pattern library                │
│          │                                                           │
│          ▼                                                           │
│   /module-bug-audit         Find all bugs (6 rounds)                 │
│          │                  + Pre-flight checks                       │
│          │                  + Existing audit guard                    │
│          │                  + Track A/B classification                │
│          │                  + KB gap analysis                         │
│          ▼                                                           │
│   /bug-intake-triage        Classify each bug (severity, track)      │
│          │                  + Duplicate bug check                     │
│          │                  + GitHub Issue created                    │
│          │                  + Reporter acknowledgement               │
│          ▼                                                           │
│   /fix-single-bug           Fix hub → guards + routes to:            │
│      │  ├── Duplicate fix guard                                      │
│      │  ├── Register in ACTIVE_BUGS.md                               │
│      │  ├── Dependency check (file overlap)                          │
│      │  ├── /fix-architecture-bug  (Track A — system bugs)           │
│      │  └── /fix-business-bug      (Track B — business bugs)         │
│      │         │                                                     │
│      │         ├── /test-and-verify (called automatically)           │
│      │         │      + Prerequisite check (PHP + PHPUnit)           │
│      │         │      + Category-specific tests                      │
│      │         │      + Smoke test checklist                         │
│      │         ├── Rollback → ROLLBACK_REGISTRY.md                   │
│      │         │                                                     │
│      │         ▼                                                     │
│      │  /learn-and-improve  Update brain + patterns + reports        │
│      │         │            + Fix velocity → FIX_VELOCITY.md         │
│      │         │            + Close in ACTIVE_BUGS.md                │
│      │         │            + Close GitHub Issue                     │
│      │         │            + Monthly metrics review                 │
│      │         │            + Brain staleness check                  │
│      │         │                                                     │
│      │         └── Loop back to /fix-single-bug for next bug         │
│      │                                                               │
│   /sprint-status            Sprint progress report (GitHub + local)  │
│   /system-health            Cross-module risk heatmap                │
│   /overall-bug-dashboard    Full dashboard + developer attribution   │
│   /validate-workflows       Self-test for workflow consistency       │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

---

## 📊 Estimation Module Stats

| Metric | Value |
|---|---|
| Brain documents | 9 (including COVERAGE_TRACKER.md) |
| Bugs found (audit) | 65 |
| Patterns matched | 17 (all from Estimation — first module audited) |
| P0 bugs | 8 |
| P1 bugs | 25 |
| P2 bugs | 22 |
| P3 bugs | 10 |
| Sprint plan | 4 sprints |

---

## 🛠️ System Files Reference

| File | Purpose | Updated By |
|---|---|---|
| `.agent/config.md` | Central env config | Developer (once per project) |
| `bug_report_AI/ACTIVE_BUGS.md` | Live bug tracker | `/fix-single-bug` + `/learn-and-improve` |
| `bug_report_AI/ROLLBACK_REGISTRY.md` | Emergency rollback index | All fix workflows |
| `bug_report_AI/FIX_VELOCITY.md` | Fix timing metrics | `/learn-and-improve` |
| `bug_report_AI/COMMON_BUG_PATTERNS.md` | Pattern library | `/manage-bug-patterns` |
| `bug_report_AI/PENDING_GITHUB_ISSUES.md` | Offline GitHub queue | `/github-bug-tracking` (fallback) |
| `bug_report_AI/CLIENT_REGISTRY.md` | All client instances | `/setup-new-client`, `/setup-existing-client` |
| `bug_report_AI/CLIENT_DEPLOYMENT_MATRIX.md` | Per-sprint deployment tracking | `/github-bug-tracking` (Part 5) |
| `bug_report_AI/DASHBOARD_REPORTS/` | Saved dashboard snapshots | `/overall-bug-dashboard` |
