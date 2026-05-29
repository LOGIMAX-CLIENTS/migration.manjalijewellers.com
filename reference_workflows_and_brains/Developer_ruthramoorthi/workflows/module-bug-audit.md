---
description: Run a 6-round AI bug audit on a module to find all bugs
---

# Phase 3 — Bug Discovery (6-Round AI Audit)

**SOP Reference**: [FINAL_SOP_BUG_REMEDIATION.md](file:///c:/xampp/htdocs/etail_development_src/SOP/FINAL_SOP_BUG_REMEDIATION.md) — Section 4 (Phase 1 — Bug Discovery)

## When to Use
- First audit of a module (after brain is built)
- Periodic re-audit to catch new bugs introduced by changes
- User says "audit this module" or "find bugs in {MODULE}"

## Prerequisites
- Module Brain must exist at `knowledge/{MODULE_NAME}/MODULE_BRAIN.md`
- If not, tell user to run `/build-module-brain` first

## Required Input
| Variable | Source |
|---|---|
| `{MODULE_NAME}` | User provides |
| `{PREFIX}` | From brain or user |
| `{CONTROLLER_FILE}` | From brain §2 file manifest |
| `{MODEL_FILE}` | From brain §2 file manifest |
| `{JS_FILE}` | From brain §2 file manifest |
| `{VIEW_DIR}` | From brain §2 file manifest |

## Steps

### 0. Pre-Scan — Read Brain & Pattern Library

// turbo
1. Read the Module Brain: `knowledge/{MODULE_NAME}/MODULE_BRAIN.md`
2. Read the common bug patterns library: `bug_report_AI/COMMON_BUG_PATTERNS.md` (if exists)

If patterns library exists, grep each known pattern against the module files. Report instant matches:
```
Pattern Match Report:
- Pattern: {PATTERN_NAME} → Found in {FILE}:{LINE} → Bug ID: {PREFIX}-P{SEQ}
```

### 1. Round 1 — Controller-Level Scan (System + Architecture)

// turbo
Read `{CONTROLLER_FILE}` and scan for:

**System Level:**
- Methods with no input validation (missing `isset`, `empty`, `is_numeric`)
- Direct use of `$_POST` / `$_GET` without sanitization
- Missing CSRF protection on POST endpoints
- Missing `try-catch` around DB operations
- `trans_commit()` called without checking `trans_status()`
- Hardcoded values (IDs, rates, percentages)

**Architecture Level:**
- Methods that return sensitive data without authorization check
- Methods that call model functions but don't check return value
- Duplicate logic across multiple methods
- Mega-methods (>200 lines) that should be split

For each bug found:
```
Bug ID: {PREFIX}-R1{SEQ}
Title: {one-line title}
Severity: P{N}
Category: {CATEGORY}
Track: A (System) / B (Business)
File: {file}
Line: {line}
Current Code: {paste exact code}
Why It's a Bug: {explanation}
Proposed Fix: {paste replacement code}
```

Save to: `bug_report_AI/{module}/ROUND_1_CONTROLLER.md`

### 2. Round 2 — DB Schema Cross-Reference (System)

// turbo
Read `{MODEL_FILE}` and cross-reference with actual DB usage:

- Columns referenced in PHP vs actual DB schema
- Dead columns (in DB but never read by PHP)
- Type mismatches (PHP sends string, DB expects int)
- Columns that should be NOT NULL but have no PHP validation
- Decimal precision mismatches
- Tables missing PRIMARY KEY
- Missing indexes on frequently-queried columns
- Missing foreign key constraints
- Tables using MyISAM that should be InnoDB (transactions)

For each bug: same format as Round 1 with ID `{PREFIX}-S{SEQ}`

Save to: `bug_report_AI/{module}/ROUND_2_SCHEMA_ANALYSIS.md`

### 3. Round 3 — Model & Data Flow Deep Dive (System + Architecture)

// turbo
Deep analysis of `{MODEL_FILE}`:

**System Level:**
- SQL queries with string concatenation (SQL injection)
- Functions that return `false`/`null` on error but callers don't check
- Missing database transactions for multi-table writes
- Transactions that commit even when a step fails

**Architecture Level:**
- N+1 query patterns (query inside a loop)
- Missing GROUP BY causing duplicate rows
- Cartesian JOINs (JOIN without proper ON condition)
- Subqueries replaceable with JOINs for performance
- Functions building arrays without validating keys before access

For each bug: ID format `{PREFIX}-R3{SEQ}`

Save to: `bug_report_AI/{module}/ROUND_3_DEEP_DIVE.md`

### 4. Round 4 — JS & View Layer (Architecture + Business)

// turbo
Read `{JS_FILE}` and view files in `{VIEW_DIR}`:

**Architecture Level (JS):**
- Variables used before defined
- Global state when should be row-scoped (check brain §7 state map)
- Event handlers attached multiple times (double-fire bugs)
- Select2/plugin init before element exists
- Hardcoded element IDs that break with multiple rows
- Wrong selector scope: `$('#id')` vs `curRow.find('.class')`

**Architecture Level (Views):**
- PHP variables used that might be undefined
- Calculations done in view that should be in model
- XSS risks (unescaped output — missing `htmlspecialchars()`)

**Business Level (JS):**
- AJAX calls with no error response handling
- Form validation only on client-side (bypass risk)
- Incorrect field-to-variable binding

For each bug: ID format `{PREFIX}-R4{SEQ}`

Save to: `bug_report_AI/{module}/ROUND_4_JS_VIEW_DEEP_DIVE.md`

### 5. Round 5 — Calculations & Data Binding (Business)

// turbo
Using the brain's §18 (Calculation Engine), verify each formula:

- Does JS implement the same formula as PHP? If mismatch → 🔴 BUG
- Do JS and PHP read from the same source fields? If not → 🔴 BUG
- Edge cases for each formula:
  - Input = 0 → correct output?
  - Input = null → crash or handle?
  - Input = negative → rejected?
  - Input = maximum → overflow?
- Calculated values stored in DB with correct precision?
- Calculated values displayed correctly in view?

For each bug: ID format `{PREFIX}-R5{SEQ}`

Save to: `bug_report_AI/{module}/ROUND_5_JS_CALCULATIONS_DEEP_DIVE.md`

### 6. Round 6 — Validation & AJAX Audit (System + Business)

// turbo
Audit ALL validation functions and AJAX handlers:

**System Level:**
- AJAX handlers that don't validate incoming data
- Inconsistent response format (not `{status, message, data}`)
- Missing error handling for DB failures in AJAX responses

**Business Level:**
- Fields with NO validation at all (brain §9 — ❌ MISSING entries)
- Fields validated client-side only (brain §9 — ⚠️ entries)
- Business rules enforced in JS but not PHP (brain §17 — SOFT rules)

For each bug: ID format `{PREFIX}-R6{SEQ}`

Save to: `bug_report_AI/{module}/ROUND_6_VALIDATION_AJAX_DEEP_DIVE.md`

### 7. Consolidate — Master Bug Report

Merge all round reports into a single consolidated report:

```markdown
# CONSOLIDATED BUG REPORT — {MODULE_NAME}

## Summary
| Metric | Count |
|---|---|
| Total Bugs | {N} |
| P0 (Critical) | {N} |
| P1 (Major) | {N} |
| P2 (Minor) | {N} |
| P3 (Cosmetic) | {N} |
| Track A (System) | {N} |
| Track B (Business) | {N} |

## Sprint Assignment
### Sprint 1 (This Week) — P0 + Critical P1
{list bugs}

### Sprint 2 (Next Week) — Remaining P1 + High P2
{list bugs}

### Sprint 3 (Backlog) — Remaining P2 + P3
{list bugs}

## Full Bug List (by severity)
{all bugs sorted P0 → P3, each with: ID, title, category, track, file, line, proposed fix}
```

Save to: `bug_report_AI/{module}/CONSOLIDATED_BUG_REPORT.md`

### 8. Update Pattern Library

Compare all found bugs against existing patterns in `bug_report_AI/COMMON_BUG_PATTERNS.md`:
- Existing pattern found in new module → add module to "Modules Found In"
- New pattern → add to library with detection rule and fix template

### 9. Report to User

Tell the user:
- Total bugs found: {N} across 6 rounds
- Breakdown by severity and track
- Sprint 1 bugs (fix immediately): list the top 5
- Whether new patterns were discovered
- Next step: `/fix-single-bug {BUG_ID}` for each Sprint 1 bug
