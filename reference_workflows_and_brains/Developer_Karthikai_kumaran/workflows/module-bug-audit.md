---
description: "Phase 1 — Run 6-round bug audit on any module. Discovers all bugs systematically."
---

# /module-bug-audit — Phase 1: Bug Discovery (6-Round AI Audit)

> **Automation Level:** 95%  
> **Source:** FINAL_SOP_BUG_REMEDIATION.md § 4, SOP_ESTIMATION_MODULE_BUG_REMEDIATION.md § 3

## Prerequisites
- Module Brain exists (run `/build-module-brain` first)
- Module variables filled at `SOP/modules/{MODULE_NAME}_variables.md`

## Step 0: Pre-Scan — Pattern Cross-Reference
Check `bug_report_AI/COMMON_BUG_PATTERNS.md`. Grep module files for known patterns.

## Step 1: Round 1 — Controller Code-Level
Focus: `{CONTROLLER_FILE}`. Scan for SQL injection, CSRF, missing rollback, undefined vars, debug artifacts.
Bug ID: `{PREFIX}-{SEQ}`

## Step 2: Round 2 — DB Schema Cross-Reference
Focus: All module tables. Scan for missing PK, wrong types, MyISAM, missing indexes.
Bug ID: `{PREFIX}-S{SEQ}`
Save: `{BUG_REPORT_DIR}/ROUND_2_SCHEMA_ANALYSIS.md`

## Step 3: Round 3 — Model & Data-Flow Deep Dive
Focus: `{MODEL_FILE}`. Scan for Cartesian JOINs, missing GROUP BY, N+1 queries.
Bug ID: `{PREFIX}-R3{SEQ}`
Save: `{BUG_REPORT_DIR}/ROUND_3_DEEP_DIVE.md`

## Step 4: Round 4 — JS Save Handler & View Layer
Focus: `{JS_FILE}` + `{VIEW_DIR}`. Scan for wrong selectors, XSS, missing AJAX error handlers.
Bug ID: `{PREFIX}-R4{SEQ}`
Save: `{BUG_REPORT_DIR}/ROUND_4_JS_VIEW_DEEP_DIVE.md`

## Step 5: Round 5 — JS Calculations & Data Binding
Focus: Calculation functions in `{JS_FILE}`. Scan for wrong variables, missing parseFloat, division by zero.
Bug ID: `{PREFIX}-R5{SEQ}`
Save: `{BUG_REPORT_DIR}/ROUND_5_JS_CALCULATIONS_DEEP_DIVE.md`

## Step 6: Round 6 — Validation Functions & AJAX Endpoints
Focus: Both JS + Controller. Scan for client-only validation, empty() on zero-valid fields.
Bug ID: `{PREFIX}-R6{SEQ}`
Save: `{BUG_REPORT_DIR}/ROUND_6_VALIDATION_AJAX_DEEP_DIVE.md`

## Step 7: Generate Consolidated Report
Create `{BUG_REPORT_DIR}/CONSOLIDATED_BUG_REPORT.md` — master index sorted by severity.

## Step 8: Generate Execution Plan
Create `{BUG_REPORT_DIR}/{MODULE}_BUG_FIX_EXECUTION_PLAN.md` with per-bug details (root cause, fix, risk, rollback, sprint).

## Step 9: Update Pattern Library
Add new patterns to `bug_report_AI/COMMON_BUG_PATTERNS.md`.

## For Each Bug Found, Document:
- Bug ID, Title, Category (1-8), Severity (P0-P3)
- Level (System 🟢 / Architecture 🟡 / Business 🔴)
- Root cause, Proposed fix, Risk, Affected files
