---
description: Run a complete 6-round bug audit on any module with pattern cross-reference and knowledge gap analysis
version: 1.2
last_updated: 2026-02-20
---

# Module Bug Audit Workflow

## Purpose
Systematically discover ALL bugs in a module using a 6-round deep analysis + pattern cross-reference. This is the **Bug Discovery** phase — it finds bugs; `/fix-single-bug` fixes them.

## Prerequisites
- Module name and file paths identified
- `bug_report_AI/COMMON_BUG_PATTERNS.md` exists (if not, create it from the template in SOP §12)
- Module Brain exists in `knowledge_brain/{MODULE_NAME}/` — if not, run `/build-module-brain` first
- Module Brain includes `METHOD_INDEX.md` with table→method reverse map

## Variables (User provides OR auto-detect from Brain)
- `{MODULE_NAME}` — e.g., Sales, Purchase, Inventory, Billing
- `{PREFIX}` — 3-char prefix for bug IDs, e.g., SAL, PUR, INV, BIL
- `{CONTROLLER_FILE}` — e.g., `{CONTROLLER_DIR}/admin_ret_sales.php`
- `{MODEL_FILE}` — e.g., `{MODEL_DIR}/ret_sales_model.php`
- `{JS_FILE}` — e.g., `{JS_DIR}/ret_sales.js`
- `{VIEW_DIR}` — e.g., `application/views/admin/sales/`
- `{KB_DIR}` — e.g., `knowledge_brain/{MODULE_NAME}/`

## Steps

### Step 0: Existing Audit Detection [Antigravity]

Before starting, check if audit data already exists for this module:

// turbo
```powershell
Test-Path "{PROJECT_ROOT}\bug_report_AI\{module_name}\*CONSOLIDATED*"
```

**If NO audit data exists** → Skip to Step 1 (fresh audit).

**If audit data EXISTS** → Present options:

| Mode | When to Use | What Happens |
|---|---|---|
| **Incremental** | Code changed since last audit, want to find new bugs | Preserve all `✅ Fixed` entries. Scan for new bugs only. Append to existing report with new bug IDs (continue sequence) |
| **Full Re-Audit** | Brain was rebuilt, or previous audit was incomplete/wrong | Back up existing audit files to `_backup_{DATE}/`, then regenerate from scratch |

#### Incremental Audit Procedure:
1. Read existing `CONSOLIDATED_BUG_REPORT.md` — extract all existing bug IDs and their statuses
2. Note the highest bug ID sequence number (e.g., if last bug was `EST-R607`, next new bug starts at `EST-R608`)
3. Run all 6 rounds as normal (Steps 2–8)
4. For each finding, check: does this match an existing bug entry?
   - **Already documented** → skip (don't duplicate)
   - **Already fixed** → verify fix still present in code. If reverted → flag as **REGRESSION** with P0 severity
   - **New bug** → add with next sequence number
5. Append new findings to existing report. Never remove existing entries.

#### Full Re-Audit Procedure:
1. Back up existing audit files:
   // turbo
   ```powershell
   $date = Get-Date -Format "yyyy-MM-dd"
   New-Item -ItemType Directory -Force -Path "{PROJECT_ROOT}\bug_report_AI\{module_name}\_backup_$date"
   Move-Item "{PROJECT_ROOT}\bug_report_AI\{module_name}\*" "{PROJECT_ROOT}\bug_report_AI\{module_name}\_backup_$date\" -Exclude "_backup_*"
   ```
2. Proceed with Steps 1–8 as a fresh audit
3. After completion, cross-reference backup to flag any previously-fixed bugs that reappeared

### Step 1: Pre-Flight Checks
// turbo
1. Read `bug_report_AI/COMMON_BUG_PATTERNS.md` — load all pattern detection rules
2. Read `knowledge_brain/{MODULE_NAME}/MODULE_BRAIN.md` — load architecture context
3. Read `knowledge_brain/{MODULE_NAME}/METHOD_INDEX.md` — load method→table mappings
4. If Brain doesn't exist → **STOP** → "Module Brain not found. Run `/build-module-brain` first."
5. Create `bug_report_AI/{module_name}/` directory if it doesn't exist

### Step 2: Round 0 — Pattern Pre-Scan [Antigravity]
Run each detection rule from `COMMON_BUG_PATTERNS.md` against all module files:

For **each** pattern in the library:
1. Execute the detection rule (grep/search) against `{CONTROLLER_FILE}`, `{MODEL_FILE}`, `{JS_FILE}`
2. If matches found → create preliminary bug entry:
   - Bug ID: `{PREFIX}-PAT-{PATTERN_ID}`
   - Status: "Pattern Match — Confirm in Round X"
   - Fix template: already available from pattern library
3. Track matches vs misses

**Report to user:**
```
Round 0: Pattern Pre-Scan Complete
├── Patterns scanned: {total_patterns}
├── Matches found: {N}
├── Confirmed bugs (instant): {list with PAT IDs}
└── Proceed to Rounds 1–6 for novel bugs
```

### Step 3: Round 1 — Controller Code-Level Analysis [Antigravity]
**Target**: `{CONTROLLER_FILE}`
**Bug ID format**: `{PREFIX}-{NNN}`

Focus areas:
- Save/update/delete paths — trace every `if/else` branch
- `trans_begin()` / `trans_commit()` / `trans_rollback()` pairing
- Undefined/misspelled variables
- Missing child table cleanup on delete
- Debug statements (`echo`, `print_r`, `var_dump`, `exit`) in production
- `$_POST` vs `$this->input->post()` usage
- CSRF/`form_secret` protection
- GET-based delete endpoints (CSRF vulnerable)

**Output**: `bug_report_AI/{module_name}/ROUND_1_CONTROLLER_ANALYSIS.md`

### Step 4: Round 2 — DB Schema Cross-Reference [Antigravity]
**Target**: All tables identified in MODULE_BRAIN + METHOD_INDEX table reverse-map
**Bug ID format**: `{PREFIX}-S{NN}`

Focus areas:
- Column type mismatches (PHP sends decimal, DB column is int)
- Missing `AUTO_INCREMENT` on primary key columns
- MyISAM engine on tables used within `trans_begin()` blocks
- Missing indexes on columns used in JOIN/WHERE clauses
- Missing foreign key constraints
- `int` columns storing percentage/decimal data

**Output**: `bug_report_AI/{module_name}/ROUND_2_SCHEMA_ANALYSIS.md`

### Step 5: Round 3 — Model & Data-Flow Deep Dive [Antigravity]
**Target**: `{MODEL_FILE}`
**Bug ID format**: `{PREFIX}-R3{NN}`

Focus areas:
- SQL injection via column name injection (`$searchField` from user input in LIKE/ORDER BY)
- Cartesian JOINs (same alias on both sides of ON clause)
- Missing `GROUP BY` with aggregate functions (`SUM`, `COUNT`, `AVG`)
- Variable typos (`$returndata` vs `$return_data`)
- Edit lifecycle data loss (fields in SELECT but not in UPDATE/re-INSERT)
- NULL dereferences on optional JOINs
- Missing WHERE scope filters (branch, company, status)

**Output**: `bug_report_AI/{module_name}/ROUND_3_DEEP_DIVE.md`

### Step 6: Round 4 — JS Save Handler & View Layer [Antigravity]
**Target**: `{JS_FILE}` (save functions) + `{VIEW_DIR}`
**Bug ID format**: `{PREFIX}-R4{NN}`

Focus areas:
- Save handler validation flow — does `form_validate` correctly aggregate?
- Always-true conditions (`.length >= 0`)
- Single-flag overwrite pattern (last section overwrites prior failures)
- Duplicate HTML IDs in view files
- XSS in view templates (unescaped `flashdata`, `echo $var`)
- Double form close tags
- GET-based delete links (CSRF)

**Output**: `bug_report_AI/{module_name}/ROUND_4_JS_VIEW_DEEP_DIVE.md`

### Step 7: Round 5 — JS Calculations & Data Binding [Antigravity]
**Target**: `{JS_FILE}` (calculation functions)
**Bug ID format**: `{PREFIX}-R5{NN}`

Focus areas:
- Calculation formula errors (copy-paste variable mismatch between similar functions)
- Division by zero (no guard on denominator)
- Class selector vs row-scoped selector (`.class` picks up wrong row)
- Undefined variables in AJAX success callbacks
- `NaN` propagation from `parseFloat('')` (should use `parseFloat('') || 0`)
- Server-side vs client-side calculation discrepancies

> [!WARNING]
> JS files are 30K+ lines. Compare parallel functions carefully — copy-paste variable mismatch is the #1 bug type.

**Output**: `bug_report_AI/{module_name}/ROUND_5_JS_CALCULATIONS_DEEP_DIVE.md`

### Step 8: Round 6 — Validation Functions & AJAX Endpoints [Antigravity]
**Target**: Both `{JS_FILE}` + `{CONTROLLER_FILE}` (AJAX handlers)
**Bug ID format**: `{PREFIX}-R6{NN}`

Focus areas:
- Missing `return false` after validation failure alert
- Logically impossible conditions (`&&`/`||` confusion)
- Validation function mutating form data as side effect
- `trans_commit()` on failure branch in AJAX handlers
- Raw `$_POST` vs `$this->input->post()` in AJAX endpoints
- Missing error response for AJAX (JS gets undetermined result)

**Output**: `bug_report_AI/{module_name}/ROUND_6_VALIDATION_AJAX_DEEP_DIVE.md`

### Step 9: Track A/B Classification [Antigravity]

Assign a **Track** to every bug found in Rounds 0–6. This determines who leads the fix.

#### Classification Rules

| Track | Criteria | Antigravity Role | Human Role |
|---|---|---|---|
| **Track A — System** | Security, transaction safety, SQL/query, schema, variable/typo, exception handling, performance, concurrency, dead code, debug artifacts | Full fix + test | Approve diff |
| **Track B — Business** | Calculation errors, wrong business data, missing business rules, workflow inconsistencies, field parity (SAVE vs UPDATE), UI logic affecting business output | Propose fix | Validate business logic before any code change |

#### Decision Rule

> If the code **runs but produces wrong business results** (wrong price, wrong tax, wrong data saved, missing fields) → **Track B**.
> Everything else (crashes, injection, performance, schema, typos that don't affect business meaning) → **Track A**.

#### Quick Classification Reference

| Bug Type | Track | Rationale |
|---|---|---|
| SQL injection (column name, concat) | **A** | Security — no business judgment needed |
| MyISAM tables in transaction blocks | **A** | Engine config — mechanical fix |
| Missing indexes | **A** | Performance — no business logic involved |
| `int` columns storing decimal values | **A** | Schema fix — type change only |
| Charset latin1 → utf8mb4 | **A** | Infrastructure — no business impact |
| Debug `echo/print_r` in production | **A** | Code cleanup — trivial |
| Undefined/misspelled variables (crash) | **A** | Code error — fix is obvious |
| N+1 queries, Cartesian JOINs | **A** | Performance — query restructure |
| Global scope leaks, race conditions | **A** | Architecture — no business meaning |
| Wrong variable in calculation (e.g., tax from wrong source) | **B** | Wrong business output — human must verify correct formula |
| Discount adds instead of subtracts | **B** | Financial calc — human must confirm |
| Missing field in UPDATE path (data loss) | **B** | Business data lost — human must verify field mapping |
| SAVE vs UPDATE field parity mismatch | **B** | Business workflow — human must confirm which is correct |
| Tax `GROUP BY` missing (wrong tax applied) | **B** | Business output wrong — human must verify |
| Selector updates all rows instead of current | **B** | Business UI behavior — human must verify intent |
| Server-side validation of prices/totals | **B** | Business rules — human must define acceptable ranges |
| `value_charge` missing from calc | **B** | Business formula incomplete — human must confirm |

For each bug in the consolidated report, add a **Track** column: `A` or `B`.

### Step 10: Consolidation [Antigravity]
1. Create `bug_report_AI/{module_name}/CONSOLIDATED_BUG_REPORT.md`:
   - Executive summary: total bugs by severity (P0/P1/P2/P3)
   - **Track distribution**: total Track A vs Track B
   - Bug distribution matrix: sub-module × round × severity
   - Master index: every bug (ID, severity, title, category, **track**, fix readiness)
   - Sprint assignment: Sprint 1 = P0 + critical P1, Sprint 2 = P1 + high P2, Sprint 3 = rest
   - Fix Pipeline Readiness checklist
   - File structure reference

2. Generate `bug_report_AI/{module_name}/{MODULE}_BUG_FIX_EXECUTION_PLAN.md`:
   For each bug, include these 11 sections:
   1. Problem Summary
   2. Root Cause
   3. Impact Assessment
   4. Location (file + line)
   5. Current Code (verbatim)
   6. Proposed Fix (code)
   7. Safety Rationale
   8. Risk Level (Low/Medium/High)
   9. Regression Checklist
   10. Rollback Plan
   11. Confirmation Gate (Track A: auto-approve, Track B: human validates)

### Step 11: KB Gap Analysis [Antigravity]
1. Compare bugs found against Module Brain coverage
2. Identify any methods, tables, or flows NOT covered in the Brain
3. Write gaps to `bug_report_AI/{module_name}/KB_GAP_ANALYSIS.md`
4. If gaps found → flag for Brain update via `/build-module-brain` (incremental update)

### Step 12: Post-Audit Pattern Library Update [Antigravity]
1. Compare ALL new bugs against `COMMON_BUG_PATTERNS.md`
2. For existing patterns found in this module → add `{MODULE_NAME}` to "Modules Found In"
3. For genuinely new patterns → add full entry:
   - Pattern ID: `PAT-{CATEGORY}-{SEQ}`
   - Description, detection rule, fix template, severity
4. Report: "Added X module references to existing patterns. Added Y new patterns."

## Post-Audit Verification Checklist
- [ ] Consolidated report has correct total counts
- [ ] All round reports use consistent bug IDs (`{PREFIX}-{ROUND}{SEQ}`)
- [ ] Every bug has Track A or Track B assigned
- [ ] Sprint priorities cover all P0 and P1 bugs
- [ ] Each bug has a concrete fix (code snippet or ALTER TABLE)
- [ ] Pattern library is updated
- [ ] KB gaps documented

## Completion Report
```
✅ Module Bug Audit complete for {MODULE_NAME}
   Round 0 (Patterns): {N} matches from {total} patterns
   Round 1 (Controller): {N} bugs
   Round 2 (Schema): {N} bugs
   Round 3 (Model): {N} bugs
   Round 4 (JS/View): {N} bugs
   Round 5 (JS Calc): {N} bugs
   Round 6 (Validation): {N} bugs
   ───────────────────────
   Total: {TOTAL} bugs
   Severity: {P0_count} P0, {P1_count} P1, {P2_count} P2, {P3_count} P3
   Track A (System): {N} | Track B (Business): {N}
   Patterns reused: {N} | New patterns added: {N}
   
   Artifacts: bug_report_AI/{module_name}/
   Next: /bug-intake-triage (for each bug) → /fix-single-bug
```
